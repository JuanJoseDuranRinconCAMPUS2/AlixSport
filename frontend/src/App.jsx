import { useState, useEffect } from "react";
import LoginPage from "./pages/login";
import RegisterPage from "./pages/RegisterPage";
import HomePage from "./pages/HomePage";
import ForgotPassword from "./pages/ForgotPasword";
import VerificationCode from "./pages/VerificacionCode";
import ProductDetail from "./pages/ProductDetail";
import AdminPanel from "./pages/AdminPanel";
import logo from "./assets/axil par.png";
import axios from "axios";

const API = import.meta.env.VITE_API;

export default function App() {
  const [user, setUser] = useState(null);
  const [page, setPage] = useState("home");
  const [popup, setPopup] = useState(null);
  const [loading, setLoading] = useState(false);
  const [recoveryEmail, setRecoveryEmail] = useState("");
  const [cart, setCart] = useState(() => {
    const stored = localStorage.getItem("cart");
    return stored ? JSON.parse(stored) : [];
  });
  const [selectedProduct, setSelectedProduct] = useState(null);

  
  useEffect(() => {
    const storedUser = localStorage.getItem("user");
    if (storedUser) {
      const parsedUser = JSON.parse(storedUser);
      setUser(parsedUser);
      getCartInfoUser(parsedUser.id);
    }
  }, []);

  const handleLogin = (userData) => {
    setUser(userData);
    localStorage.setItem("user", JSON.stringify(userData));
    getCartInfoUser(userData.id);
    setPage("home");

  };

  const handleLogout = () => {
    localStorage.removeItem("user");
    localStorage.removeItem("cart");
    setUser(null);
    setCart([]);
  };

  const handleViewProduct = (product) => {
    setSelectedProduct(product);
    setPage("product-detail");
  };

  const getCartInfoUser = async (userId) => {
    const { data } = await axios.post(`${API}/DetallesByUser`, 
      {
        "idUsuario": `${userId}`
      }
    ); 
    
    if (data) {
      setCart(data);
      localStorage.setItem("cart", JSON.stringify(data));
    }
  };

  const addToCart = async (productId, quantity) => {
    setLoading(true);
    try {
      const { data } = await axios.post(`${API}/cambiarCantidadCarrito`, 
        {
          "idUsuario": `${user.id}`,
          "idProducto": `${productId}`,
          "cantidad": `${quantity}`
        }
      ); 
      
      await getCartInfoUser(user.id);
      console.log(data);
      switch (data.mensaje) {
        case "Cantidad actualizada":
            setPopup({
                status: "ok",
                mensaje: "Ya se agrego el producto al carrito",
            });
          break;
        
        case "Producto agregado al carrito":
            setPopup({
                status: "ok",
                mensaje: "Producto agregado al carrito",
            });
          break;
          
        default:
          setPopup({
                status: "ok",
                mensaje: data.mensaje,
            });
          break;
      }
      
      
    } catch (err) {
      console.log("Error al agregar al carrito", err);
    }finally { 
      setLoading(false);
    }
  };

  const removeFromCart = async (productId) => {

    const { data } = await axios.post(`${API}/cambiarCantidadCarrito`, 
      {
        "idUsuario": `${user.id}`,
        "idProducto": `${productId}`,
        "cantidad": `0`
        
      }
    ); 
    await getCartInfoUser(user.id);
    console.log(data);
    
  };

  const updateQuantity = async (productId, newQuantity) => {
    const { data } = await axios.post(`${API}/cambiarCantidadCarrito`, 
      {
        "idUsuario": `${user.id}`,
        "idProducto": `${productId}`,
        "cantidad": `${newQuantity}`
        
      }
    ); 
    await getCartInfoUser(user.id);
    console.log(data);
  };

  const handleForgotPassword = () => {
    setPage("forgot-password");
  };

  const handleCodeSent = (email) => {
    setRecoveryEmail(email);
    setPage("verification");
  };

  const handleBackToEmail = () => {
    setPage("forgot-password");
  };

  const handleVerificationSuccess = () => {
    setPage("login");
    setRecoveryEmail("");
  };

  return (
    <>
      {page === "home" && (
        <HomePage
          user={user}
          onLoginClick={() => setPage("login")}
          onRegisterClick={() => setPage("register")}
          onLogout={handleLogout}
          cart={cart}
          onAddToCart={addToCart}
          onRemoveFromCart={removeFromCart}
          onUpdateQuantity={updateQuantity}
          onViewProduct={handleViewProduct}
          onAdminClick={() => setPage("admin")}
          setPopup={setPopup}
          setLoading={setLoading}
        />
      )}

      {page === "login" && (
        <LoginPage 
          onLogin={handleLogin} 
          onRegisterClick={() => setPage("register")} 
          onForgotPasswordClick={handleForgotPassword}
        />
      )}

      {page === "register" && <RegisterPage onGoLogin={() => setPage("login")} />}

      {page === "forgot-password" && (
        <ForgotPassword 
          onBackToLogin={() => setPage("login")}
          onCodeSent={handleCodeSent}
        />
      )}

      {page === "verification" && (
        <VerificationCode 
          email={recoveryEmail}
          onBackToEmail={handleBackToEmail}
          onVerificationSuccess={handleVerificationSuccess}
        />
      )}

      {page === "product-detail" && selectedProduct && (
        <ProductDetail
          product={selectedProduct}
          onBack={() => setPage("home")}
          onAddToCart={addToCart}
          user={user}
        />
      )}

      {page === "admin" && (
        <AdminPanel
          onBack={() => setPage("home")}
        />
      )}

      {popup && (
        <div className="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50 animate-[fadeIn_0.3s_ease-out]">
          <div
            className={`
              w-[90%] max-w-md rounded-2xl p-6 shadow-2xl border 
              animate-[popUp_0.25s_ease-out]
              ${popup.status === "ok"
                ? "bg-black border-green-400"
                : "bg-black border-red-500"
              }
            `}
          >
          <div className="flex items-center gap-3 mb-4">
            <img src={logo} alt="logo" className="w-12 h-12" />
            <h2
              className={`text-2xl font-bold tracking-wide 
              ${popup.status === "ok" ? "text-green-400" : "text-red-500"}`}
            >
              {popup.status === "ok" ? "¡Éxito!" : "Error"}
            </h2>
          </div>
                  
          <p className="text-neutral-200 text-lg">{popup.mensaje}</p>
                    
          <button
            onClick={() => {
            if (popup.status === "ok") {
              
            }
                      setPopup(null);
                    }}
                    className="mt-6 w-full py-2 font-semibold rounded-lg 
                    bg-green-400 text-black hover:bg-green-300 transition-shadow shadow-green-500/40 shadow-md"
                    >
                    {popup.status === "ok" ? "Continuar" : "Cerrar"}
                  </button>
                </div>
              </div>
      )}
                    
      {loading && (
        <div className="fixed inset-0 bg-black bg-opacity-70 backdrop-blur-sm flex items-center justify-center z-50">
          <div className="bg-neutral-900 p-8 rounded-xl shadow-xl border border-green-400 flex flex-col items-center animate-pulse">
            <img src={logo} alt="Logo" className="w-24 mb-4" />
            <p className="text-green-400 text-xl font-semibold tracking-wide">Cargando...</p>
            <div className="mt-5 h-2 w-40 bg-neutral-800 rounded-full overflow-hidden">
              <div className="h-full w-full bg-green-400 animate-[progress_1.2s_linear_infinite]"></div>
            </div>
          </div>
        </div>
      )}
    </>
  );
}