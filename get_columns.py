import pandas as pd
import json
import sys

# Load the dataset
try:
    df = pd.read_csv('dataset/ObesityPredictionDataset.csv')
    
    # Get column names
    columns = df.columns.tolist()
    
    # Print the column names as JSON
    print(json.dumps(columns))
except Exception as e:
    print(json.dumps([]))
    sys.exit(1)