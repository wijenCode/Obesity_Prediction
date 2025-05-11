#!/usr/bin/env python
import pandas as pd
import numpy as np
import joblib
import json
import argparse
import os
import sys

def main():
    # Parse command line arguments
    parser = argparse.ArgumentParser(description='Make obesity predictions from CSV data')
    parser.add_argument('--csv', required=True, help='Path to CSV file with feature data')
    args = parser.parse_args()

    # Check if the file exists
    if not os.path.exists(args.csv):
        print(json.dumps(["Error: File not found"]))
        sys.exit(1)

    try:
        # Load the model
        model_path = os.path.join(os.path.dirname(os.path.dirname(os.path.abspath(__file__))), 
                                 'models', 'obesity_pipeline_SVM.sav')
        
        if not os.path.exists(model_path):
            print(json.dumps(["Error: Model file not found"]))
            sys.exit(1)
            
        pipeline = joblib.load(model_path)
        
        # Load the data
        df = pd.read_csv(args.csv)
        
        # Ensure all required columns are present
        required_columns = [
            'Gender', 'Age', 'Height', 'Weight', 'family_history_of_overweight', 
            'FAVC', 'FCVC', 'NCP', 'CAEC', 'SMOKE', 'CH2O', 'SCC', 
            'FAF', 'TUE', 'CALC', 'MTRANS'
        ]
        
        missing_columns = set(required_columns) - set(df.columns)
        
        if missing_columns:
            print(json.dumps([f"Error: Missing columns: {', '.join(missing_columns)}"]))
            sys.exit(1)
        
        # Data preprocessing - ensure correct data types and formats
        # Convert binary columns to lowercase for consistency
        binary_cols = ['family_history_of_overweight', 'FAVC', 'SMOKE', 'SCC']
        for col in binary_cols:
            if col in df.columns:
                df[col] = df[col].astype(str).str.lower()
        
        # Ensure ordinal columns have correct values
        ordinal_cols = ['CAEC', 'CALC']
        valid_ordinal_values = ['no', 'Sometimes', 'Frequently', 'Always']
        for col in ordinal_cols:
            if col in df.columns:
                # Check if all values are valid
                invalid_values = df[~df[col].isin(valid_ordinal_values)][col].unique()
                if len(invalid_values) > 0:
                    print(json.dumps([f"Error: Invalid values in {col}: {', '.join(map(str, invalid_values))}"]))
                    sys.exit(1)
        
        # Check Gender column
        if 'Gender' in df.columns:
            df['Gender'] = df['Gender'].astype(str)
            invalid_gender = df[~df['Gender'].isin(['Male', 'Female'])]['Gender'].unique()
            if len(invalid_gender) > 0:
                print(json.dumps([f"Error: Invalid gender values: {', '.join(invalid_gender)}"]))
                sys.exit(1)
        
        # Check MTRANS column
        valid_mtrans = ['Automobile', 'Bike', 'Motorbike', 'Public_Transportation', 'Walking']
        if 'MTRANS' in df.columns:
            invalid_mtrans = df[~df['MTRANS'].isin(valid_mtrans)]['MTRANS'].unique()
            if len(invalid_mtrans) > 0:
                print(json.dumps([f"Error: Invalid transportation values: {', '.join(invalid_mtrans)}"]))
                sys.exit(1)
        
        # Convert numeric columns to float
        numeric_cols = ['Age', 'Height', 'Weight', 'FCVC', 'NCP', 'CH2O', 'FAF', 'TUE']

        for col in numeric_cols:
            if col in df.columns:
                try:
                    # Jika kolom adalah 'Age', biarkan dalam format int
                    if col == 'Age':
                        df[col] = df[col].astype(int)
                    else:
                        df[col] = df[col].astype(float)
                except ValueError:
                    print(json.dumps([f"Error: Non-numeric values in column {col}"]))
                    sys.exit(1)
        
        # Make predictions
        try:
            predictions = pipeline.predict(df)
            
            # Convert predictions to list for JSON serialization
            predictions_list = predictions.tolist()
            
            # Output the predictions as JSON
            print(json.dumps(predictions_list))
        except Exception as e:
            print(json.dumps([f"Error during prediction: {str(e)}"]))
            sys.exit(1)
        
    except Exception as e:
        print(json.dumps([f"Error: {str(e)}"]))
        sys.exit(1)

if __name__ == "__main__":
    main()