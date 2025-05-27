import pandas as pd
import matplotlib.pyplot as plt
import seaborn as sns
import numpy as np
import os
import sys

# Create images directory if it doesn't exist
if not os.path.exists('images'):
    os.makedirs('images')

# Get the correlation type from command line argument
correlation_type = sys.argv[1] if len(sys.argv) > 1 else 'heatmap'

# Load the dataset
try:
    df = pd.read_csv('dataset/ObesityPredictionDataset.csv')
except Exception as e:
    print(f"Error loading dataset: {e}")
    sys.exit(1)

# Set the style for the plot
plt.style.use('dark_background')

# Convert categorical variables to numeric for correlation analysis
df_numeric = df.copy()

# Handle categorical columns for correlation analysis
for col in df.columns:
    if pd.api.types.is_object_dtype(df[col]) or pd.api.types.is_categorical_dtype(df[col]):
        df_numeric[col] = pd.factorize(df[col])[0]

# Generate correlation visualization based on the selected type
if correlation_type == 'heatmap':
    # Create correlation matrix
    corr_matrix = df_numeric.corr()
    
    # Create larger figure for better readability
    plt.figure(figsize=(16, 12))
    
    # Create a mask for the upper triangle
    mask = np.triu(np.ones_like(corr_matrix, dtype=bool))
    
    # Create heatmap with improved aesthetics
    sns.heatmap(corr_matrix, mask=mask, annot=True, fmt='.2f', cmap='viridis',
                linewidths=0.5, cbar_kws={"shrink": 0.8})
    
    plt.title('Feature Correlation Heatmap', fontsize=18, pad=20)
    plt.tight_layout()
    plt.savefig('images/correlation_visualization.png', bbox_inches='tight', dpi=100)

elif correlation_type == 'pairplot':
    # Select only numeric columns for pairplot
    numeric_cols = df.select_dtypes(include=['number']).columns.tolist()
    
    # If there are too many numeric columns, select a subset
    if len(numeric_cols) > 5:
        # Try to include BMI and another important column if available
        important_cols = ['BMI', 'Weight', 'Age', 'Height']
        selected_cols = [col for col in important_cols if col in numeric_cols]
        
        # Add more columns until we have 5
        for col in numeric_cols:
            if col not in selected_cols and len(selected_cols) < 5:
                selected_cols.append(col)
    else:
        selected_cols = numeric_cols
    
    # If 'NObeyesdad' is in the dataset and has been converted to numeric, use it as hue
    if 'NObeyesdad' in df.columns:
        # Create a simplified version with fewer categories for better visualization
        df['obesity_category'] = df['NObeyesdad'].apply(
            lambda x: 'Underweight' if 'Insufficient' in str(x) 
            else 'Normal' if 'Normal' in str(x)
            else 'Overweight' if 'Overweight' in str(x)
            else 'Obesity'
        )
        
        pairplot = sns.pairplot(df[selected_cols + ['obesity_category']], 
                               hue='obesity_category', 
                               diag_kind='kde',
                               plot_kws={'alpha': 0.6},
                               palette='viridis')
    else:
        pairplot = sns.pairplot(df[selected_cols], 
                              diag_kind='kde',
                              plot_kws={'alpha': 0.6})
    
    pairplot.fig.suptitle('Pairwise Feature Relationships', y=1.02, fontsize=16)
    plt.tight_layout()
    plt.savefig('images/correlation_visualization.png', bbox_inches='tight', dpi=100)

elif correlation_type == 'target_correlation':
    # Make sure the target column exists
    if 'NObeyesdad' not in df.columns:
        print("Target column 'NObeyesdad' not found")
        sys.exit(1)
    
    # Get correlations with target
    corr_with_target = df_numeric.corr()['NObeyesdad'].sort_values(ascending=False)
    
    # Remove self-correlation
    corr_with_target = corr_with_target.drop('NObeyesdad')
    
    # Create figure
    plt.figure(figsize=(14, 10))
    
    # Create bar plot of correlations
    bars = sns.barplot(x=corr_with_target.index, y=corr_with_target.values, 
                       palette='viridis')
    
    # Add value labels on top of bars
    for i, v in enumerate(corr_with_target.values):
        plt.text(i, v + 0.02 if v >= 0 else v - 0.08, 
                 f'{v:.2f}', ha='center', fontsize=10)
    
    plt.title('Feature Correlation with Obesity Level', fontsize=18, pad=20)
    plt.xlabel('Features', fontsize=14)
    plt.ylabel('Correlation with Obesity Level', fontsize=14)
    plt.xticks(rotation=45, ha='right')
    
    # Add a horizontal line at y=0
    plt.axhline(y=0, color='gray', linestyle='-', alpha=0.3)
    
    # Add color coding for positive/negative correlation
    for i, v in enumerate(corr_with_target.values):
        color = '#4af24a' if v > 0 else '#f24a4a'
        bars.patches[i].set_facecolor(color)
    
    plt.tight_layout()
    plt.savefig('images/correlation_visualization.png', bbox_inches='tight', dpi=100)

print(f"{correlation_type} visualization generated successfully")