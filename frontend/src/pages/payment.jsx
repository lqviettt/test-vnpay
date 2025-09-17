import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { apiCreatePayment } from "../api";

export default function Payment() {
    const [amount, setAmount] = useState("");
    const [bankCode, setBankCode] = useState("");
    const [orderId, setOrderId] = useState("");
    const [message, setMessage] = useState("");
    const navigate = useNavigate();

    async function handlePayment(e) {
        e.preventDefault();
        if (!amount || isNaN(amount) || Number(amount) <= 0) {
            setMessage("Vui lòng nhập số tiền hợp lệ!");
            return;
        }
        if (!bankCode) {
            setMessage("Vui lòng chọn phương thức thanh toán!");
            return;
        }
        if (!orderId) {
            setMessage("Vui lòng nhập Order ID!");
            return;
        }
        let token = localStorage.getItem("token");
        let res = await apiCreatePayment(token, amount, bankCode, orderId);
        const data = res.data || {};
        if (data.payment) {
            if (data.payment.payment_url) {
                window.location.href = data.payment.payment_url; // Redirect to VNPAY URL
            } else {
                setMessage("Tạo thanh toán thành công! Mã: " + (data.code || data.id));
                setTimeout(() => navigate("/history"), 1200);
            }
        } else {
            setMessage("Thanh toán thất bại!");
        }
    }

    return (
        <div style={{ display: "flex", justifyContent: "center", alignItems: "center", height: "80vh" }}>
            <div style={{ background: "#fff", padding: 32, borderRadius: 12, boxShadow: "0 2px 16px #0002", minWidth: 320 }}>
                <h2 style={{ textAlign: "center", marginBottom: 24, color: "#1976d2" }}>Tạo thanh toán</h2>
                <form onSubmit={handlePayment} style={{ display: "flex", flexDirection: "column", gap: 16 }}>
                    <input
                        type="text"
                        value={orderId}
                        onChange={e => setOrderId(e.target.value)}
                        placeholder="Order ID"
                        style={{ padding: 10, borderRadius: 6, border: "1px solid #ccc" }}
                    />
                    <input
                        type="number"
                        value={amount}
                        onChange={(e) => setAmount(e.target.value)}
                        placeholder="Số tiền"
                        style={{ padding: 10, borderRadius: 6, border: "1px solid #ccc" }}
                        min={1}
                    />
                    <select value={bankCode} onChange={e => setBankCode(e.target.value)} style={{ padding: 10, borderRadius: 6, border: "1px solid #ccc" }}>
                        <option value="">Chọn phương thức thanh toán</option>
                        <option value="VNPAY">VNPAYQR</option>
                        <option value="VNBANK">VNBANK</option>
                        <option value="INTCARD">INTCARD</option>
                        {/* Thêm các ngân hàng khác nếu cần */}
                    </select>
                    <button type="submit" style={{ padding: 10, borderRadius: 6, background: "#1976d2", color: "#fff", border: "none", fontWeight: "bold" }}>Thanh toán</button>
                </form>
                {message && (
                    <div style={{ marginTop: 18, textAlign: "center", color: message.includes("thành công") ? "#388e3c" : "#d32f2f", fontWeight: "bold" }}>{message}</div>
                )}
            </div>
        </div>
    );
}
