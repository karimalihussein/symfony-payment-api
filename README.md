# 🚀 Payment API Project

This project is a **Payment Processing API** built using **Symfony 6.4**, supporting multiple payment gateways such as **ACI** and **Shift4**, with **Swagger** documentation.

---

## 🛠️ **Running the Project Using Docker**

Follow these steps to set up and run the project:

1. **Ensure Docker is installed on your machine**.
2. **Run the following command to build and start the project:**

```bash
docker-compose up -d --build

⸻

🔹 Available APIs

📌 POST /api/payment/process

Processes a payment using a payment gateway like ACI or Shift4.

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
	•	It is fully Dockerized, so no additional dependencies are required. Just use docker-compose to start the project.
	•	No database is needed, as the API only handles payment processing.

⸻

📩 Contact

If you encounter any issues, feel free to reach out! 😃🚀


```
