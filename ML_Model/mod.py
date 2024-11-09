from sqlalchemy import create_engine
import mysql.connector
import pandas as pd
from sklearn.model_selection import train_test_split
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.ensemble import RandomForestClassifier
from sklearn.metrics import classification_report
import pymysql

# Define MySQL connection details
host = 'vultr-prod-cfbaea5c-14b5-4c87-b3e2-9929eed22e05-vultr-prod-4489.vultrdb.com'
user = 'vultradmin'
password = 'special_password'
database = 'defaultdb'
port = 16751

# Load and preprocess data
data = pd.read_csv("C:\\Users\\DELL\\Female_Cyber_Cases_100K_Online_Threats.csv", encoding='ISO-8859-1')
data = pd.read_csv("C:\\Users\\DELL\\Downloads\\INFOSYS COURSE\\cybercrime\\Cyber_Cases_100K_Updated.csv", encoding='ISO-8859-1')
data.dropna(inplace=True)

priority_mapping = {'High': 0, 'Moderate': 1, 'Low': 2}
data['Priority Level'] = data['Priority Level'].map(priority_mapping)
X = data['Case Type']
y = data['Priority Level']
X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

vectorizer = TfidfVectorizer(stop_words='english')
X_train_vec = vectorizer.fit_transform(X_train)
X_test_vec = vectorizer.transform(X_test)

model = RandomForestClassifier(n_estimators=100, random_state=42)
model.fit(X_train_vec, y_train)
predictions = model.predict(X_test_vec)
print(classification_report(y_test, predictions, target_names=['High', 'Moderate', 'Low']))

# SQLAlchemy connection to check connection status
engine = create_engine(f'mysql+pymysql://{user}:{password}@{host}:{port}/{database}')
try:
    with engine.connect() as connection:
        print("SQLAlchemy connection successful.")
except Exception as e:
    print("SQLAlchemy connection error:", e)

# MySQL Connector connection for table creation and data insertion
try:
    db = mysql.connector.connect(
        host=host,
        user=user,
        password=password,
        database=database,
        port=port,
        auth_plugin='mysql_native_password'
    )

    if db.is_connected():
        print("Database connected successfully.")
        cursor = db.cursor()

        # Creating tables with error handling
        try:
            # Create `model_complaints` table
            cursor.execute('''
            CREATE TABLE IF NOT EXISTS model_complaints (
                model_complaint_id INT PRIMARY KEY AUTO_INCREMENT,
                complaint_id INT UNSIGNED NOT NULL,
                user_id INT UNSIGNED NOT NULL,
                description TEXT NOT NULL,
                `low_priority` INT DEFAULT 0,
                `medium_priority` INT DEFAULT 0,
                `high_priority` INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (complaint_id) REFERENCES complaints(id),
                FOREIGN KEY (user_id) REFERENCES users(id)
            );
            ''')
            print("model_complaints table created or exists already.")

            # Create `gov_send` table
            cursor.execute('''
            CREATE TABLE IF NOT EXISTS gov_send (
                id INT PRIMARY KEY AUTO_INCREMENT,
                description TEXT NOT NULL,
                user_id INT UNSIGNED NOT NULL,
                complaint_id INT UNSIGNED NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id),
                FOREIGN KEY (complaint_id) REFERENCES complaints(id)
            );
            ''')
            print("gov_send table created or exists already.")

            # Commit table creation
            db.commit()

            # Fetch complaints data and process predictions
            query = "SELECT * FROM complaints"
            userComplaint = pd.read_sql(query, db)
            X_test = userComplaint['description'] 
            user_ids = userComplaint['user_id'] 
            complaint_ids = userComplaint['id'] 
            
            # Convert to integer types
            user_ids = user_ids.astype(int)
            complaint_ids = complaint_ids.astype(int)

            # Insert prediction results into the database
            for i, user_complaint in enumerate(X_test):
                high_priority = 1 if predictions[i] == 0 else 0
                medium_priority = 1 if predictions[i] == 1 else 0
                low_priority = 1 if predictions[i] == 2 else 0

                cursor.execute('''
                INSERT INTO model_complaints (description,  `high_priority`, `medium_priority`, `low_priority`, user_id, complaint_id) 
                VALUES (%s, %s, %s, %s, %s, %s)
                ''', (X_test[i], int(high_priority), int(medium_priority), int(low_priority), int(user_ids.iloc[i]), int(complaint_ids.iloc[i])))

                # Insert high-priority cases into `gov_send`
                if high_priority == 1:
                    cursor.execute('''
                    INSERT INTO gov_send (description, user_id, complaint_id)
                    VALUES (%s, %s, %s)
                    ''', (X_test[i], int(user_ids.iloc[i]), int(complaint_ids.iloc[i])))

            # Commit the inserts
            db.commit()
            print("Data inserted into model_complaints and gov_send successfully.")

        except Exception as e:
            print("Error during table creation or data insertion:", e)
            db.rollback()  # Roll back if an error occurs

    else:
        print("Database connection failed.")
except mysql.connector.Error as e:
    print("Error connecting to the database with MySQL Connector:", e)
finally:
    # Close the database connection
    if db.is_connected():
        cursor.close()
        db.close()
        print("MySQL connection closed.")
