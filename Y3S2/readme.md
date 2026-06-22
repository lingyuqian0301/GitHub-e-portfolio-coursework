# 📊 Data Engineering & Analytics Portfolio

**Ling Yu Qian** 
*Bachelor of Computer Science (Data Engineering), Universiti Teknologi Malaysia (UTM)* 

Welcome to my technical portfolio. This repository compiles a series of extensive academic and practical case studies demonstrating my progression in cloud data architecture, big data processing, and machine learning. Each document reflects a hands-on approach to solving complex, real-world data bottlenecks—from migrating legacy on-premise databases to architecting predictive deep learning models.

Below are my critical reflections and technical analyses for each core project and industry engagement.

---

## I. Enterprise Cloud Architecture: The PPG Inventory Risk Management System
**Associated File:** `SECP3843 - PPG CASE STUDY S1 GROUP 2 (5).pdf`

* **Project Scope:** Engineered an end-to-end Microsoft Azure data pipeline to identify Recoverable Assets (RA) and Magna Carta (MC) dead stock for PPG Industries, automating 100% financial write-down provisions.
* **Technical Stack:** Azure Data Factory, Databricks (PySpark), Synapse Analytics, Power BI.
* **Reflection & Analysis:** This case study bridged the gap between raw data engineering and tangible financial impact. By replacing a reactive, spreadsheet-based legacy system with a robust three-tier **Medallion Architecture (Bronze, Silver, Gold)**, I learned the critical importance of data immutability and schema enforcement. Building the Silver-to-Gold PySpark notebooks taught me how to translate strict accounting rules (e.g., identifying materials with zero consumption for 9+ months) into scalable code. Structuring the final data into a **Fact Constellation (Galaxy) Schema** within Azure Synapse proved essential, as it allowed our Power BI dashboard to dynamically link material shortages directly to affected customer sales orders via complex DAX measures. 

---

## II. Big Data Processing: Brazilian Educational Census via Apache Spark
**Associated File:** `tutorial 2 Apache Spark.pdf`

* **Project Scope:** Processed 2.2GB of fragmented Brazilian census data (~3 million rows) into a query-optimized data warehouse.
* **Technical Stack:** Apache Spark, Python, PostgreSQL, Parquet.
* **Reflection & Analysis:**
  This project served as a rigorous lesson in hardware optimization and distributed computing. Processing massive CSV files locally exposed the limitations of traditional libraries like Pandas. By configuring Spark sessions with customized memory allocations (`spark.driver.memory`, `10g`), I was able to successfully process the data. The most valuable technical takeaway was witnessing the physical transformation of data: converting the raw CSVs into a compressed, column-oriented **Parquet** format drastically reduced query latency. Moving this data into a well-defined **Star Schema** in PostgreSQL solidified my understanding of how upstream data formatting directly dictates downstream BI efficiency.

---

## III. On-Premise to Cloud Migration: Microsoft Azure End-to-End Pipeline
**Associated File:** `tutorial 1 Microsoft Azure.pdf`

* **Project Scope:** Migrated the local Adventure Works SQL Server database to the Azure cloud to analyze customer purchasing behavior and gender demographics.
* **Technical Stack:** Azure Data Factory (Self-Hosted Integration Runtime), ADLS Gen2, Databricks, Synapse Serverless SQL.
* **Reflection & Analysis:**
  While architecting the pipeline was a success, the true learning occurred during troubleshooting. A major obstacle I encountered was configuring the `dbutils.fs.mount()` command in Databricks, which continually failed due to cluster security restrictions. Resolving this by configuring a new cluster with Single User access mode taught me a vital industry lesson: cloud data engineering is not just about writing transformation scripts; it is equally about mastering infrastructure security, identity management, and access control. This project emphasized that real-world engineering requires resilience and the ability to debug beyond standard tutorial instructions.

---

## IV. Deep Learning: Image Classification using CNNs
**Associated File:** `tutorial 3 AI Algorithm.pdf`

* **Project Scope:** Designed and evaluated a Convolutional Neural Network (CNN) against a traditional Artificial Neural Network (ANN) to classify the CIFAR-10 image dataset.
* **Technical Stack:** Python, TensorFlow, Keras, Matplotlib.
* **Reflection & Analysis:**
  This tutorial provided a deep dive into the architectural superiority of CNNs for computer vision. While the ANN destroyed spatial hierarchies by flattening the images (yielding only 49% accuracy), the CNN preserved these relationships through local connectivity and parameter sharing, achieving a **68.38% test accuracy**. However, the most critical analytical takeaway was identifying model behavior. Observing a **9.52% gap** between our training accuracy (77.9%) and test accuracy revealed classic **overfitting**. Furthermore, analyzing the confusion matrix highlighted that the model struggled to differentiate animals (cats/dogs) due to shared visual traits at low resolutions ($32\times32$), while vehicles were identified with high precision. This reinforced that evaluating precision, recall, and F1-scores is just as important as building the model itself.

---

## V. Architectural Paradigms & System Design

### 1. ETL, ELT, and DBT
**Associated File:** `26 _TechnicalReport.pdf`
* **Reflection & Analysis:**
  This research fundamentally shifted my perspective on modern data stacks. I analyzed how the explosion of big data made traditional ETL transformation servers a severe bottleneck. By decoupling storage and compute, ELT allows organizations to leverage the immense parallel processing power of cloud warehouses like Snowflake and BigQuery. Exploring the **Data Build Tool (DBT)** highlighted the modernization of analytics, treating SQL transformations with software engineering rigor (version control, automated testing). However, I also learned to view these tools critically; without proper governance, ELT can lead to high cloud compute cost volatility and decentralized "data swamps."

### 2. BI Design: Operational Dashboards
**Associated File:** `tutorial article.pdf`
* **Reflection & Analysis:**
  Before writing a single line of code, an engineer must understand the business need. This project honed my system analysis skills by forcing me to map out explicit **User Stories and Acceptance Criteria** for top management. By architecting distinct Sales and Production Data Marts, I learned the importance of domain isolation. Designing the dashboard to calculate **Overall Equipment Effectiveness (OEE)**—by combining machine availability, performance, and quality metrics into a single gauge chart—demonstrated how to translate raw manufacturing variables into high-level strategic insights.

---

## VI. Industry Exposure & Professional Development

### 1. Industry Visit: PPG Coatings (Malaysia) Sdn. Bhd.
* **Context:** An on-site industrial visit for Data Engineering students to the manufacturing and warehousing facilities of PPG Coatings. 
* **Reflection & Analysis:** 
  Walking the floor of the PPG warehouse provided a visceral understanding of the data I had been modeling in the *PPG Inventory Risk Management* case study. Seeing the physical space dedicated to storing raw materials made the abstract concept of "Magna Carta" dead stock incredibly real. It became immediately apparent why our Power BI dashboard needed to be hyper-accurate; physical warehousing space is finite and expensive. This visit bridged the gap between code and physical operations, cementing the realization that effective data engineering directly alleviates physical supply chain bottlenecks. 

### 2. Industry Talk: iZeno 
* **Context:** An industry talk by representatives from iZeno, focusing on enterprise IT modernization and Industry 4.0 solution provisioning.
* **Reflection & Analysis:**
  The session with iZeno provided deep insights into how large enterprises transition from legacy infrastructures to modern cloud architectures. The discussion around their role as an Industry 4.0 solution provider heavily resonated with my experience migrating the on-premise SQL Server to Microsoft Azure. The speakers emphasized that the true challenge in cloud migration isn't just moving data, but maintaining data governance and implementing CI/CD pipelines during the transition. This talk reinforced the necessity of treating data pipelines as rigorously as software development lifecycles.

### 3. Industry Talk: Telekom Malaysia (TM)
* **Context:** An industry talk hosted by TM detailing the "Technology Information System & 4.0th Industrial Revolution".
* **Reflection & Analysis:**
  TM’s presentation on the 4th Industrial Revolution covered the integration of advanced automation, Internet of Things (IoT), and big data analytics. The speakers highlighted how these technologies are actively reshaping industries and optimizing telecommunication processes. Listening to how TM handles massive volumes of real-time network data made me re-evaluate my approach to data processing. While my Apache Spark project handled 2.2GB of batch data effectively, the TM talk highlighted that the future of the industry lies in real-time streaming analytics. It challenged me to start thinking beyond batch-processing frameworks and to explore how edge-computing and 5G will impact data pipeline latency in the near future.