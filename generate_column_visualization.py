import pandas as pd
import matplotlib.pyplot as plt
import seaborn as sns
import numpy as np
import os
import sys

# Create images directory if it doesn't exist
if not os.path.exists('images'):
    os.makedirs('images')

# Get the column name from command line argument
column_name = sys.argv[1] if len(sys.argv) > 1 else 'Age'

# Load the dataset
try:
    df = pd.read_csv('dataset/ObesityPredictionDataset.csv')
except Exception as e:
    print(f"Error loading dataset: {e}")
    sys.exit(1)

# Set the style for the plot
plt.style.use('dark_background')
fig, ax = plt.subplots(figsize=(12, 8))

# Check if the column exists
if column_name not in df.columns:
    print(f"Column {column_name} not found in dataset")
    sys.exit(1)

# Determine the type of plot based on the data type
if pd.api.types.is_numeric_dtype(df[column_name]):
    # Numeric data: histogram with KDE
    sns.histplot(data=df, x=column_name, kde=True, ax=ax, color='#4a9cf2')
    ax.set_title(f'Distribution of {column_name}', fontsize=16, pad=20)
    ax.set_xlabel(column_name, fontsize=12)
    ax.set_ylabel('Frequency', fontsize=12)
    
    # Add mean and median lines
    mean_val = df[column_name].mean()
    median_val = df[column_name].median()
    
    ax.axvline(mean_val, color='#f24a4a', linestyle='--', linewidth=2, label=f'Mean: {mean_val:.2f}')
    ax.axvline(median_val, color='#4af24a', linestyle='-.', linewidth=2, label=f'Median: {median_val:.2f}')
    ax.legend(fontsize=12)
    
elif column_name == 'NObeyesdad' or pd.api.types.is_categorical_dtype(df[column_name]) or df[column_name].nunique() < 15:
    # Categorical data: countplot
    value_counts = df[column_name].value_counts().sort_index()
    
    # If there are too many categories, use barplot instead
    if df[column_name].nunique() > 10:
        plt.figure(figsize=(14, 8))
        sns.countplot(data=df, x=column_name, palette='viridis', ax=ax)
        plt.xticks(rotation=45, ha='right')
    else:
        # Create a custom color palette
        colors = sns.color_palette('viridis', len(value_counts))
        sns.barplot(x=value_counts.index, y=value_counts.values, palette=colors, ax=ax)
    
    ax.set_title(f'Distribution of {column_name}', fontsize=16, pad=20)
    ax.set_xlabel(column_name, fontsize=12)
    ax.set_ylabel('Count', fontsize=12)
    
    # Add count labels on top of bars
    for i, count in enumerate(value_counts.values):
        ax.text(i, count + 0.1, str(count), ha='center', fontsize=10)
else:
    # For text data or other types: show value counts
    value_counts = df[column_name].value_counts()
    explode = [0.1] * len(value_counts)
    
    ax.pie(value_counts, labels=value_counts.index, autopct='%1.1f%%', 
           startangle=90, explode=explode, shadow=True, 
           colors=sns.color_palette('viridis', len(value_counts)))
    ax.set_title(f'Distribution of {column_name}', fontsize=16, pad=20)
    ax.axis('equal')

# Add relationship with target if column is not the target
if column_name != 'NObeyesdad' and 'NObeyesdad' in df.columns:
    # Create a separate plot for relationship with target
    fig2, ax2 = plt.subplots(figsize=(12, 6))
    
    if pd.api.types.is_numeric_dtype(df[column_name]):
        # For numeric columns, use boxplot
        sns.boxplot(x='NObeyesdad', y=column_name, data=df, ax=ax2, palette='viridis')
        ax2.set_title(f'{column_name} by Obesity Level', fontsize=16, pad=20)
        ax2.set_xlabel('Obesity Level', fontsize=12)
        ax2.set_ylabel(column_name, fontsize=12)
        plt.xticks(rotation=45, ha='right')
    else:
        # For categorical columns, use heatmap of cross-tabulation
        cross_tab = pd.crosstab(df[column_name], df['NObeyesdad'], normalize='index')
        sns.heatmap(cross_tab, annot=True, cmap='viridis', fmt='.2f', ax=ax2)
        ax2.set_title(f'Relationship between {column_name} and Obesity Level', fontsize=16, pad=20)
        ax2.set_xlabel('Obesity Level', fontsize=12)
        ax2.set_ylabel(column_name, fontsize=12)
    
    # Adjust layout and save the relationship plot
    plt.tight_layout()
    plt.savefig('images/relationship_with_target.png', bbox_inches='tight', dpi=100)

# Adjust layout and save the main plot
plt.tight_layout()
plt.savefig('images/column_visualization.png', bbox_inches='tight', dpi=100)

print(f"Visualization for {column_name} generated successfully")