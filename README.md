# 🚀 Payment API Project

This project is a **Payment Processing API** built with **Symfony 6.4**, supporting multiple payment gateways like **ACI** and **Shift4**. It is designed to be modular, scalable, and easy to set up. The project is fully Dockerized for seamless installation and execution, but it can also be run locally without Docker. Additionally, it provides **console commands** for testing payment processing directly from the terminal.

---

## 🛠 Installation & Running the Project

### Prerequisites

Ensure you have the following installed:

- **For Docker Setup**:
  - Docker 🐳
  - Docker Compose
- **For Non-Docker Setup**:
  - PHP 8.1 or higher
  - Composer
  - Symfony CLI (optional but recommended)

---

### 🐳 Running the Project Using Docker

#### Steps to Run the Project

1️⃣ **Clone the repository**:

```bash
git clone https://github.com/karimalihussein/symfony-payment-api.git
cd symfony-payment-api
```

2️⃣ **Build and start the container**:

```bash
docker-compose up -d --build
```

3️⃣ **Verify that the containers are running**:

```bash
docker ps
```

4️⃣ **Access the application**:

- API Base URL: [http://localhost:8080](http://localhost:8080)

---

### 🖥 Running the Project Without Docker

#### Steps to Run the Project

1️⃣ **Clone the repository**:

```bash
git clone https://github.com/karimalihussein/symfony-payment-api.git
cd symfony-payment-api
```

2️⃣ **Install dependencies using Composer**:

```bash
composer install
```

3️⃣ **Start the Symfony local server**:

```bash
symfony serve
```

4️⃣ **Access the application**:

- API Base URL: [http://localhost:8000](http://localhost:8000)

---

## 🔹 Available APIs

### 📌 Process Payment

- **Endpoint**: `/api/payment/process`
- **Method**: `POST`
- **Description**: Processes a payment using a selected payment gateway (ACI, Shift4, etc.).

#### ✅ Request Body (JSON)

```json
{
  "gateway": "aci",
  "amount": 200,
  "currency": "USD",
  "cardNumber": "4111111111111111",
  "cardExpYear": "2025",
  "cardExpMonth": "12",
  "cardCvv": "123"
}
```

---

### 📌 Execute Request Using cURL

```bash
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
```

---

### ✅ Successful Response

```json
{
  "status": "success",
  "message": "Payment processed successfully",
  "data": {
    "transactionId": "txn_123456",
    "amount": 200,
    "currency": "USD"
  }
}
```

### ❌ Error Response

```json
{
  "status": "error",
  "message": "Invalid input data"
}
```

---

## 🖥 Console Commands

The project also provides **console commands** to process payments directly from the terminal. This is useful for testing and debugging.

### 📌 Process Payment via Command

1️⃣ **Process Payment with ACI Gateway**:

```bash
php bin/console app:process-payment aci 200
```

2️⃣ **Process Payment with Shift4 Gateway**:

```bash
php bin/console app:process-payment shift4 450
```

#### Example Output:

```bash
Payment processed successfully!
Gateway: aci
Amount: 200
Transaction ID: txn_123456
```

---

## 🎯 Additional Notes

- ✔ **Fully Dockerized** – No additional dependencies are required when using Docker.
- ✔ **No Database Needed** – The API solely handles payment processing.
- ✔ **Modular & Scalable** – Can be extended to support additional payment gateways.
- ✔ **Console Commands** – Test payment processing directly from the terminal.

---

## 📩 Contact

If you encounter any issues or have questions, feel free to reach out! 🚀💡
