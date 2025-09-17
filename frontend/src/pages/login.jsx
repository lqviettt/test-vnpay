import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { apiLogin } from "../api";

export default function Login() {
  const [name, setName] = useState("");
  const [password, setPassword] = useState("");
  const [message, setMessage] = useState("");
  const navigate = useNavigate();

  async function handleLogin(e) {
    e.preventDefault();
    if (!name || !password) {
      setMessage("Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu!");
      return;
    }
    let data = await apiLogin(name, password);
    if (data.access_token) {
      localStorage.setItem("token", data.access_token);
      setMessage("Đăng nhập thành công!");
      setTimeout(() => {
        navigate("/payment");
        window.location.reload();
      }, 500);
    } else {
      setMessage("Đăng nhập thất bại! Kiểm tra lại thông tin.");
    }
  }

  return (
    <div style={{ display: "flex", justifyContent: "center", alignItems: "center", height: "80vh" }}>
      <div style={{ background: "#fff", padding: 32, borderRadius: 12, boxShadow: "0 2px 16px #0002", minWidth: 320 }}>
        <h2 style={{ textAlign: "center", marginBottom: 24, color: "#1976d2" }}>Đăng nhập</h2>
        <form onSubmit={handleLogin} style={{ display: "flex", flexDirection: "column", gap: 16 }}>
          <input
            value={name}
            onChange={(e) => setName(e.target.value)}
            placeholder="Tên đăng nhập"
            style={{ padding: 10, borderRadius: 6, border: "1px solid #ccc" }}
            autoFocus
          />
          <input
            type="password"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            placeholder="Mật khẩu"
            style={{ padding: 10, borderRadius: 6, border: "1px solid #ccc" }}
          />
          <button type="submit" style={{ padding: 10, borderRadius: 6, background: "#1976d2", color: "#fff", border: "none", fontWeight: "bold" }}>Đăng nhập</button>
        </form>
        {message && (
          <div style={{ marginTop: 18, textAlign: "center", color: message.includes("thành công") ? "#388e3c" : "#d32f2f", fontWeight: "bold" }}>{message}</div>
        )}
      </div>
    </div>
  );
}