Here is your README.md file, well-formatted and structured for clarity and professionalism:

⸻

🚀 Payment API Project

This project is a Payment Processing API built with Symfony 6.4, supporting multiple payment gateways like ACI and Shift4.
It is fully Dockerized, allowing seamless installation and execution without additional dependencies.

⸻

🛠 Installation & Running the Project Using Docker

Prerequisites

Ensure you have the following installed:
• Docker 🐳
• Docker Compose

Steps to Run the Project

1️⃣ Clone the repository

git clone https://github.com/karimalihussein/symfony-payment-api.git
cd symfony-payment-api

2️⃣ Build and start the container

docker-compose up -d --build

3️⃣ Verify that the containers are running

docker ps

4️⃣ Access the application
• API Base URL: http://localhost:8080

⸻

🔹 Available APIs

📌 Process Payment
• Endpoint: /api/payment/process
• Method: POST
• Description: Processes a payment using a selected payment gateway (ACI, Shift4, etc.).

✅ Request Body (JSON)

{
"gateway": "aci",
"amount": 200,
"currency": "USD",
"cardNumber": "4111111111111111",
"cardExpYear": "2025",
"cardExpMonth": "12",
"cardCvv": "123"
}

⸻

📌 Execute Request Using cURL

curl -X POST "http://localhost:8080/api/payment/process" \
 -H "Content-Type: application/json" \
 -d '{
"gateway": "aci",
"amount": 200,
"currency": "USD",
"cardNumber": "4111111111111111",
"cardExpYear": "2025",
"cardExpMonth": "12",
"cardCvv": "123"
}'

⸻

✅ Successful Response

{
"status": "success",
"message": "Payment processed successfully",
"data": {
"transactionId": "txn_123456",
"amount": 200,
"currency": "USD"
}
}

❌ Error Response

{
"status": "error",
"message": "Invalid input data"
}

⸻

🎯 Additional Notes

✔ Fully Dockerized – No additional dependencies are required.
✔ No Database Needed – The API solely handles payment processing.
✔ Modular & Scalable – Can be extended to support additional payment gateways.

⸻

📩 Contact

If you encounter any issues, feel free to reach out! 🚀💡

⸻

Now, simply save this as README.md in your project directory. 🚀🔥
