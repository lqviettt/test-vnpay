import { BrowserRouter, Routes, Route, Link, Navigate, useLocation, useNavigate } from "react-router-dom";
import Login from "./pages/login";
import Payment from "./pages/payment";
import Result from "./pages/result";
import History from "./pages/history";

function RequireAuth({ children }) {
  const token = localStorage.getItem("token");
  const location = useLocation();
  if (!token) {
    return <Navigate to="/" state={{ from: location }} replace />;
  }
  return children;
}

function MainLayout() {
  const token = localStorage.getItem("token");
  const navigate = useNavigate();
  function handleLogout() {
    localStorage.removeItem("token");
    navigate("/");
  }
  return (
    <div style={{ minHeight: "100vh", width: "100vw", display: "flex", flexDirection: "column", justifyContent: "center", alignItems: "center", background: "#f5f5f5" }}>
      <nav style={{ margin: "40px 0 30px 0", padding: "24px 48px", background: "#fff", borderRadius: 16, boxShadow: "0 2px 16px #0002", fontSize: 28, fontWeight: "bold", display: "flex", alignItems: "center", justifyContent: "center", gap: 32, minWidth: 500 }}>
        {!token && (
          <Link to="/" style={{ textDecoration: "none", color: "#1976d2" }}>Login</Link>
        )}
        {token && (
          <>
            <Link to="/payment" style={{ textDecoration: "none", color: "#1976d2" }}>Payment</Link>
            <Link to="/history" style={{ textDecoration: "none", color: "#1976d2" }}>History</Link>
            <button onClick={handleLogout} style={{ padding: "8px 24px", borderRadius: 8, background: "#d32f2f", color: "#fff", border: "none", fontWeight: "bold", fontSize: 22, cursor: "pointer" }}>Logout</button>
          </>
        )}
      </nav>
      <div style={{ width: "100%", display: "flex", justifyContent: "center", alignItems: "center" }}>
        <Routes>
          <Route path="/" element={<Login />} />
          <Route path="/payment" element={
            <RequireAuth>
              <Payment />
            </RequireAuth>
          } />
          <Route path="/result" element={
            <RequireAuth>
              <Result />
            </RequireAuth>
          } />
          <Route path="/history" element={
            <RequireAuth>
              <History />
            </RequireAuth>
          } />
        </Routes>
      </div>
    </div>
  );
}

export default function App() {
  return (
    <BrowserRouter>
      <MainLayout />
    </BrowserRouter>
  );
}