import { useEffect, useState } from "react";
import { apiPaymentReturn } from "../api";

export default function Result() {
  const [result, setResult] = useState(null);
  useEffect(() => {
    async function fetchResult() {
      let query = window.location.search.substring(1);
      let data = await apiPaymentReturn(query);
      setResult(data);
    }
    fetchResult();
  }, []);

  return (
    <div style={{ display: "flex", justifyContent: "center", alignItems: "center", minHeight: "80vh" }}>
      <div style={{ background: "#fff", padding: 32, borderRadius: 12, boxShadow: "0 2px 16px #0002", minWidth: 400 }}>
        <h2 style={{ textAlign: "center", marginBottom: 24, color: "#1976d2" }}>Kết quả thanh toán</h2>
        {result ? (
          <div style={{ fontSize: 16 }}>
            <div style={{ marginBottom: 12 }}>
              <b>Mã giao dịch:</b> {result.code || result.payment_code || "-"}
            </div>
            <div style={{ marginBottom: 12 }}>
              <b>Số tiền:</b> {result.amount ? result.amount.toLocaleString() + " đ" : "-"}
            </div>
            <div style={{ marginBottom: 12 }}>
              <b>Phương thức:</b> {result.bank_code || "-"}
            </div>
            <div style={{ marginBottom: 12 }}>
              <b>Trạng thái:</b> <span style={{ color: result.status === "success" ? "#388e3c" : "#d32f2f", fontWeight: "bold" }}>{result.status || "-"}</span>
            </div>
            <div style={{ marginBottom: 12 }}>
              <b>Thời gian:</b> {result.created_at ? new Date(result.created_at).toLocaleString() : "-"}
            </div>
            <div style={{ marginTop: 24, textAlign: "center" }}>
              <pre style={{ background: "#f5f5f5", padding: 12, borderRadius: 8, fontSize: 13 }}>{JSON.stringify(result, null, 2)}</pre>
            </div>
          </div>
        ) : (
          <div style={{ textAlign: "center", color: "#1976d2" }}>Đang tải...</div>
        )}
      </div>
    </div>
  );
}
