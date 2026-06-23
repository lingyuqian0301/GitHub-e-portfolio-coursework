# Business Intelligence Project Reflection

## Project Overview
This reflection summarises the Business Intelligence project undertaken by the "Decision Makers" group. The project analysed two takeaway restaurants in London and combined restaurant sales data with historical weather data to deliver actionable insights and strategic recommendations.

## 1. Technical Skill Development
- **ETL Processes with Alteryx**: Built automated data pipelines using Alteryx Designer.
  - Used tools like Filter, Select, Data Cleansing, and Unique.
  - Removed invalid records by validating Quantity and Product Price values.
  - Eliminated duplicate records to ensure clean input data.
- **Data Transformation & Feature Engineering**:
  - Used Formula tools to derive temporal features such as Month, Season, and Is_Weekend from raw dates.
  - These features enabled deeper seasonal and trend analyses.
- **Data Visualization with Power BI**:
  - Created two interactive dashboards: Restaurant Performance Analysis and Weather Impact Analysis.
  - Selected charts intentionally: line charts for monthly revenue trends, scatter plots for precipitation correlation, and heatmaps for customer demand by season and weather.

## 2. Overcoming Challenges
- **Data Consistency**:
  - Restaurant order datasets and the London weather dataset used different date structures.
  - Standardised all date fields using `DateTimeParse()` so data could be integrated accurately.
- **Complex Data Integration**:
  - Merged five datasets without losing integrity.
  - Built a structured workflow:
    - joined orders with prices by Item Name,
    - tagged records for Restaurant 1 or Restaurant 2,
    - combined branches with Union,
    - joined weather data via a standardized Date field.
- **Data Validation**:
  - Used Browse tools regularly to inspect intermediate outputs.
  - This ensured correct merges and prevented data loss.

## 3. Key Business Insights Discovered
- **Branch Discrepancies**:
  - Restaurant 2 significantly outperformed Restaurant 1, generating £0.68 million versus £0.45 million.
- **Product Concentration**:
  - Sales were heavily concentrated in a few items, especially Plain Papadum (29K units) and Pilau Rice (18K units).
- **The Power of Weather**:
  - Dry weather generated the highest revenue (£640,000) and the greatest customer demand across all seasons.
  - Heavy Rain recorded the lowest revenue (£50,000).
  - Cool temperatures (5–12°C) were the most favorable for sales.

## 4. Strategic Value and Conclusion
This project demonstrated how Business Intelligence transforms retrospective data into forward-looking strategy.

### Strategic recommendations
- Increase staffing and maintain higher inventory for Plain Papadum and Pilau Rice during forecasted dry weather.
- Implement targeted promotions and discounts during heavy rain to mitigate expected revenue drops.
- Investigate Restaurant 2's operational strategies and replicate successful approaches at Restaurant 1.

### Final reflection
As the "Decision Makers" team, we successfully built an interactive benchmarking system linking weather patterns to restaurant profitability. Management now has empirical evidence to support data-driven decisions rather than relying on assumptions.
