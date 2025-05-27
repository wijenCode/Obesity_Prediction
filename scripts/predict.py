import pandas as pd
import numpy as np
import joblib
import json
import argparse
import os
import sys
from sklearn.metrics import accuracy_score, classification_report, confusion_matrix
import uuid

def encode_labels(labels):
    """Convert string labels to numeric codes"""
    label_mapping = {
        'Insufficient_Weight': 0,
        'Normal_Weight': 1,
        'Overweight_Level_I': 2,
        'Overweight_Level_II': 3,
        'Obesity_Type_I': 4,
        'Obesity_Type_II': 5,
        'Obesity_Type_III': 6
    }
    return [label_mapping.get(label, -1) for label in labels]

def decode_labels(encoded_labels):
    """Convert numeric codes back to string labels"""
    label_mapping = {
        0: 'Insufficient_Weight',
        1: 'Normal_Weight',
        2: 'Overweight_Level_I',
        3: 'Overweight_Level_II',
        4: 'Obesity_Type_I',
        5: 'Obesity_Type_II',
        6: 'Obesity_Type_III'
    }
    return [label_mapping.get(label, 'Unknown') for label in encoded_labels]

def calculate_accuracy_and_save_results(df, predictions, actual_labels, model_name):
    """Calculate accuracy and save detailed results to CSV"""
    
    # Encode actual labels if they are strings
    if isinstance(actual_labels[0], str):
        actual_encoded = encode_labels(actual_labels)
    else:
        actual_encoded = actual_labels
    
    # Calculate accuracy
    accuracy = accuracy_score(actual_encoded, predictions)
    
    # Create results DataFrame
    results_df = df.copy()
    results_df['Actual_Label'] = actual_labels
    results_df['Predicted_Label'] = decode_labels(predictions)
    results_df['Predicted_Code'] = predictions
    results_df['Correct'] = [1 if a == p else 0 for a, p in zip(actual_encoded, predictions)]
    
    # Generate unique filename for results
    results_filename = f"prediction_results_{model_name}_{uuid.uuid4().hex[:8]}.csv"
    results_path = os.path.join('uploads', results_filename)
    
    # Save results to CSV
    results_df.to_csv(results_path, index=False)
    
    return accuracy, results_path

def main():
    # Parse command line arguments
    parser = argparse.ArgumentParser(description='Make obesity predictions from CSV data')
    parser.add_argument('--csv', required=True, help='Path to CSV file with feature data')
    parser.add_argument('--model', required=True, choices=['SVM', 'KNN', 'DT', 'NN'], 
                        help='Model to use for prediction (SVM, KNN, DT, NN)')
    parser.add_argument('--labeled', action='store_true', 
                        help='Indicates if the dataset contains actual labels for accuracy calculation')
    args = parser.parse_args()

    # Check if the file exists
    if not os.path.exists(args.csv):
        print(json.dumps({"error": "File not found"}))
        sys.exit(1)

    try:
        # Determine the model file based on the selected model
        model_filename_map = {
            'SVM': 'obesity_pipeline_SVM.sav',
            'KNN': 'obesity_pipeline_KNN.sav',
            'DT': 'obesity_pipeline_DT.sav',
            'NN': 'obesity_pipeline_NN.sav'
        }
        
        model_filename = model_filename_map.get(args.model)
        if not model_filename:
            print(json.dumps({"error": f"Invalid model selection: {args.model}"}))
            sys.exit(1)
        
        # Load the model
        model_path = os.path.join(os.path.dirname(os.path.dirname(os.path.abspath(__file__))), 
                                 'models', model_filename)
        
        if not os.path.exists(model_path):
            print(json.dumps({"error": f"Model file not found: {model_filename}"}))
            sys.exit(1)
            
        pipeline = joblib.load(model_path)
        
        # Load the data
        df = pd.read_csv(args.csv)
        
        # Check if dataset is labeled
        has_labels = 'Obesity' in df.columns
        actual_labels = None
        
        if has_labels:
            actual_labels = df['Obesity'].tolist()
            # Remove the label column for prediction
            df_features = df.drop('Obesity', axis=1)
        else:
            df_features = df.copy()
        
        # Ensure all required columns are present
        required_columns = [
            'Gender', 'Age', 'Height', 'Weight', 'family_history_of_overweight', 
            'FAVC', 'FCVC', 'NCP', 'CAEC', 'SMOKE', 'CH2O', 'SCC', 
            'FAF', 'TUE', 'CALC', 'MTRANS'
        ]
        
        missing_columns = set(required_columns) - set(df_features.columns)
        
        if missing_columns:
            print(json.dumps({"error": f"Missing columns: {', '.join(missing_columns)}"}))
            sys.exit(1)
        
        # Data preprocessing - ensure correct data types and formats
        # Convert binary columns to lowercase for consistency
        binary_cols = ['family_history_of_overweight', 'FAVC', 'SMOKE', 'SCC']
        for col in binary_cols:
            if col in df_features.columns:
                df_features[col] = df_features[col].astype(str).str.lower()
        
        # Ensure ordinal columns have correct values
        ordinal_cols = ['CAEC', 'CALC']
        valid_ordinal_values = ['no', 'Sometimes', 'Frequently', 'Always']
        for col in ordinal_cols:
            if col in df_features.columns:
                # Check if all values are valid
                invalid_values = df_features[~df_features[col].isin(valid_ordinal_values)][col].unique()
                if len(invalid_values) > 0:
                    print(json.dumps({"error": f"Invalid values in {col}: {', '.join(map(str, invalid_values))}"}))
                    sys.exit(1)
        
        # Check Gender column
        if 'Gender' in df_features.columns:
            df_features['Gender'] = df_features['Gender'].astype(str)
            invalid_gender = df_features[~df_features['Gender'].isin(['Male', 'Female'])]['Gender'].unique()
            if len(invalid_gender) > 0:
                print(json.dumps({"error": f"Invalid gender values: {', '.join(invalid_gender)}"}))
                sys.exit(1)
        
        # Check MTRANS column
        valid_mtrans = ['Automobile', 'Bike', 'Motorbike', 'Public_Transportation', 'Walking']
        if 'MTRANS' in df_features.columns:
            invalid_mtrans = df_features[~df_features['MTRANS'].isin(valid_mtrans)]['MTRANS'].unique()
            if len(invalid_mtrans) > 0:
                print(json.dumps({"error": f"Invalid transportation values: {', '.join(invalid_mtrans)}"}))
                sys.exit(1)
        
        # Convert numeric columns to appropriate types
        numeric_cols = ['Age', 'Height', 'Weight', 'FCVC', 'NCP', 'CH2O', 'FAF', 'TUE']

        for col in numeric_cols:
            if col in df_features.columns:
                try:
                    # If column is 'Age', keep it as int
                    if col == 'Age':
                        df_features[col] = df_features[col].astype(int)
                    else:
                        df_features[col] = df_features[col].astype(float)
                except ValueError:
                    print(json.dumps({"error": f"Non-numeric values in column {col}"}))
                    sys.exit(1)
        
        # Make predictions
        try:
            predictions = pipeline.predict(df_features)
            
            # Convert predictions to list for JSON serialization
            predictions_list = predictions.tolist()
            
            # Prepare result object
            result = {
                "predictions": predictions_list
            }
            
            # If we have actual labels, calculate accuracy and save detailed results
            if has_labels and actual_labels:
                accuracy, results_file = calculate_accuracy_and_save_results(
                    df, predictions, actual_labels, args.model
                )
                result["accuracy"] = accuracy
                result["results_file"] = results_file
                result["total_samples"] = len(predictions)
                result["correct_predictions"] = sum(1 for a, p in zip(encode_labels(actual_labels), predictions) if a == p)
            
            # Output the result as JSON
            print(json.dumps(result))
            
        except Exception as e:
            print(json.dumps({"error": f"Error during prediction: {str(e)}"}))
            sys.exit(1)
        
    except Exception as e:
        print(json.dumps({"error": f"Error: {str(e)}"}))
        sys.exit(1)

if __name__ == "__main__":
    main()