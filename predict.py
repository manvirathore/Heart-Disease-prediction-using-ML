import sys
import joblib
import pandas as pd
import numpy as np
import json  # Make sure to import json

# Load the models and scaler
logistic_model = joblib.load('logistic_model.pkl')
knn_model = joblib.load('knn_model.pkl')
random_forest_model = joblib.load('random_forest_model.pkl')
svm_model = joblib.load('svm_model.pkl')
decision_tree_model = joblib.load('decision_tree_model.pkl')
naive_bayes_model = joblib.load('naive_bayes_model.pkl')
scaler = joblib.load('scaler.pkl')

# Get input values from command-line arguments
input_data = np.array(sys.argv[1:], dtype=float).reshape(1, -1)

# Scale the input data
input_data_scaled = scaler.transform(input_data)

# Make predictions using all models
predictions = {
    "Logistic Regression": int(logistic_model.predict(input_data_scaled)[0]),  # Convert to int
    "KNN": int(knn_model.predict(input_data_scaled)[0]),  # Convert to int
    "Random Forest": int(random_forest_model.predict(input_data_scaled)[0]),  # Convert to int
    "SVM": int(svm_model.predict(input_data_scaled)[0]),  # Convert to int
    "Decision Tree": int(decision_tree_model.predict(input_data_scaled)[0]),  # Convert to int
    "Naive Bayes": int(naive_bayes_model.predict(input_data_scaled)[0])  # Convert to int
}

# Print predictions in JSON format
output_json = json.dumps(predictions)  # Serialize to JSON
print(output_json)  # Print the JSON output
