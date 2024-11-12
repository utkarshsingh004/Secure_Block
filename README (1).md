# Cyber Complaint Classification System - Machine Learning Setup 🚀

[![Python](https://img.shields.io/badge/Python-3.7%2B-blue)](https://www.python.org/downloads/) [![scikit-learn](https://img.shields.io/badge/scikit--learn-0.24%2B-orange)](https://scikit-learn.org/stable/) [![pandas](https://img.shields.io/badge/pandas-1.0%2B-lightgrey)](https://pandas.pydata.org/) [![MySQL](https://img.shields.io/badge/MySQL-Connector-yellow)](https://dev.mysql.com/doc/connector-python/en/) [![SQLAlchemy](https://img.shields.io/badge/SQLAlchemy-1.3%2B-green)](https://www.sqlalchemy.org/)

This guide walks you through the setup, installation, and execution of the **Machine Learning** component for the **Cyber Complaint Classification System**. Follow these steps to get up and running!

---

## Prerequisites 📋

- **Python**: [Download Python 3.7+](https://www.python.org/downloads/)
- **MySQL Server**: Ensure you have a MySQL server running or accessible.

---

## Table of Contents

1. [Python Installation](#1-install-python)
2. [Set Up a Virtual Environment](#2-set-up-a-virtual-environment)
3. [Install Required Libraries](#3-install-required-libraries)
4. [Prepare the Dataset](#4-download-or-prepare-the-dataset)
5. [Run the Machine Learning Script](#5-run-the-machine-learning-script)
6. [Verify Installation](#6-verify-installation)

---

## 1. Install Python 🐍

- **Download**: Go to [python.org](https://www.python.org/downloads/) and download the latest version of Python (3.7 or higher).
- **Install**: Run the installer. **Check the box labeled `Add Python to PATH`** during installation.

## 2. Set Up a Virtual Environment (Recommended) 🛠️

It’s recommended to use a virtual environment to manage dependencies separately from your main Python installation.

1. **Open a command prompt (Windows) or terminal (MacOS/Linux)**.
2. **Create a virtual environment** (named `env` here):
 python -m venv env
3.	Activate the virtual environment:
o	Windows:
                                     .\env\Scripts\activate
o	MacOS/Linux:
    source env/bin/activate
4.	When activated, you’ll see (env) in your terminal prompt.
________________________________________
3. Install Required Libraries 📦
With your virtual environment active, run the following command to install the necessary packages:
pip install pandas scikit-learn mysql-connector-python pymysql SQLAlchemy
Package Overview
Package	Description
pandas	Data manipulation and CSV reading.
scikit-learn	Machine learning model building and evaluation.
mysql-connector-python	Connects to MySQL databases for data storage and retrieval.
pymysql	Enables MySQL connection with SQLAlchemy.
SQLAlchemy	Establishes connections and interacts with MySQL databases in Python.
________________________________________
4. Download or Prepare the Dataset 📂
•	Place your CSV files (e.g., Female_Cyber_Cases_100K_Online_Threats.csv and Cyber_Cases_100K_Updated.csv) in a location accessible by the script.
•	Update the file path in your script to match the dataset location on your system.
________________________________________
5. Run the Machine Learning Script 🧑💻
1.	Open your preferred Python editor or IDE (such as VS Code or Jupyter Notebook).
2.	Load the machine learning script.
3.	Run the script to train the model and view evaluation metrics.
________________________________________
6. Verify Installation ✅
•	Success Check: If the model runs and outputs a classification report, your setup is complete! 🎉
•	Troubleshoot: If you encounter a missing package error, install the package directly by running:
                        pip install <missing_package>
________________________________________
This setup guide should help you get the machine learning component of the Cyber Complaint Classification System up and running smoothly. Feel free to reach out if you need further assistance or have any questions!

Additional Resources 📚
•	Python Documentation
•	scikit-learn Documentation
•	MySQL Connector for Python
________________________________________

