import { useState, useEffect } from "react";
import { apiPaymentHistory } from "../api";

export default function History() {
    const [history, setHistory] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        loadHistory();
    }, []);

    async function loadHistory() {
        setLoading(true);
        let token = localStorage.getItem("token");
        let res = await apiPaymentHistory(token);
        let payments = [];
        if (res && res.data) {
            if (Array.isArray(res.data)) {
                payments = res.data;
            } else if (Array.isArray(res.data.payments)) {
                payments = res.data.payments;
            }
        }
        if (payments.length > 0) {
            setHistory(payments.slice(0, 5));
        } else {
            setHistory([]);
        }
        setLoading(false);
    }

    return (
        <div style={{ display: "flex", justifyContent: "center", alignItems: "center", minHeight: "80vh" }}>
            <div style={{ background: "#fff", padding: 32, borderRadius: 12, boxShadow: "0 2px 16px #0002", minWidth: 400 }}>
                <h2 style={{ textAlign: "center", marginBottom: 24, color: "#000" }}>Lịch sử thanh toán gần nhất</h2>
                {loading ? (
                    <div style={{ textAlign: "center", color: "#000" }}>Đang tải...</div>
                ) : history.length > 0 ? (
                    <table style={{ width: "100%", borderCollapse: "collapse", marginTop: 16 }}>
                        <thead>
                            <tr style={{ background: "#1976d2", color: "#000" }}>
                                <th style={{ padding: 10, border: "1px solid #000" }}>ID</th>
                                <th style={{ padding: 10, border: "1px solid #000" }}>Mã giao dịch</th>
                                <th style={{ padding: 10, border: "1px solid #000" }}>Mã đơn hàng</th>
                                <th style={{ padding: 10, border: "1px solid #000" }}>Số tiền</th>
                                <th style={{ padding: 10, border: "1px solid #000" }}>Phương thức</th>
                                <th style={{ padding: 10, border: "1px solid #000" }}>Trạng thái</th>
                                <th style={{ padding: 10, border: "1px solid #000" }}>Thời gian</th>
                            </tr>
                        </thead>
                        <tbody>
                            {history.map((payment) => (
                                <tr key={payment.id} style={{ background: "#302828ff" }}>
                                    <td style={{ padding: 10, border: "1px solid #000" }}>{payment.id}</td>
                                    <td style={{ padding: 10, border: "1px solid #000" }}>{payment.code}</td>
                                    <td style={{ padding: 10, border: "1px solid #000" }}>{payment.order_id}</td>
                                    <td style={{ padding: 10, border: "1px solid #000" }}>{payment.amount.toLocaleString()} đ</td>
                                    <td style={{ padding: 10, border: "1px solid #000" }}>{payment.bank_code}</td>
                                    <td style={{ padding: 10, border: "1px solid #000", color: payment.status === "success" ? "#a49696ff" : "#bc5a5aff" }}>{payment.status}</td>
                                    <td style={{ padding: 10, border: "1px solid #000" }}>{new Date(payment.created_at).toLocaleString()}</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                ) : (
                    <div style={{ textAlign: "center", color: "#000" }}>Không có lịch sử thanh toán.</div>
                )}
            </div>
        </div>
    );
}
