const API_URL = "http://127.0.0.1:8000/api";

export async function apiLogin(name, password) {
  const res = await fetch(`${API_URL}/login`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ name, password }),
  });
  return res.json();
}

export async function apiCreatePayment(token, amount, bank_code, order_id) {
  const res = await fetch(`${API_URL}/auth/vnpay/create-payment`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      Authorization: "Bearer " + token,
    },
    body: JSON.stringify({ amount, bank_code, order_id }),
  });
  return res.json();
}


export async function apiPaymentReturn(query) {
  const res = await fetch(`${API_URL}/auth/vnpay/payment-return?${query}`);
  return res.json();
}

export async function apiPaymentHistory(token) {
  const res = await fetch(`${API_URL}/auth/vnpay/payment-history`, {
    headers: { Authorization: "Bearer " + token },
  });
  return res.json();
}
