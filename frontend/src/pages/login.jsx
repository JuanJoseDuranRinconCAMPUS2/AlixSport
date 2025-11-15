import { useState } from "react";
import { motion } from "framer-motion";
import logo from "../assets/axil par.png";
import axios from "axios";

const API = import.meta.env.VITE_API;

export default function LoginPage({ 
  onLogin, 
  onRegisterClick, 
  onForgotPasswordClick
}) {

  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [userData, setUserData] = useState({});
  const [popup, setPopup] = useState(null);
  const [loading, setLoading] = useState(false);



  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);

    try {

      const { data } = await axios.post(`${API}/loginUsuario`, 
          {
            "email": email,
            "password": password
          }
        ); 
        
        if (data.status == 'ok') {
          console.log(data);
          setPopup({
            status: data.status,
            mensaje: "Se ha iniciado seccion correctamente, bienvenido " + data.nombre ,
          });
          setUserData(data);
        } else {
          setPopup({
          status: data.status,
          mensaje: data.mensaje,
        });
        }
    } catch (error) {
      console.log(error);
      
      setPopup({
        status: "error",
        mensaje: "Error de conexión con el servidor",
      });
    } finally { 
      setLoading(false);
    }
  };

  return (
    <motion.div
      initial={{ opacity: 0 }}
      animate={{ opacity: 1 }}
      transition={{ duration: 1.2 }}
      className="min-h-screen flex items-center justify-center bg-gradient-to-br from-neutral-900 via-neutral-800 to-neutral-700"
    >

      <motion.div
        initial={{ y: -40, opacity: 0 }}
        animate={{ y: 0, opacity: 1 }}
        transition={{ duration: 1, type: "spring" }}
        className="bg-neutral-900/90 backdrop-blur-md p-10 rounded-2xl shadow-2xl w-96 border border-neutral-700"
      >

        <div className="flex flex-col items-center mb-6">
          <motion.img
            src={logo}
            alt="Logo"
            className="w-30 h-30 object-contain mb-3"
            initial={{ scale: 0 }}
            animate={{ scale: 1 }}
            transition={{ duration: 0.8, type: "spring" }}
          />

          <h2 className="text-2xl font-bold text-white tracking-wide mb-4">
            ALIX SPORT
          </h2>

        </div>

        <motion.form
          onSubmit={handleSubmit}
          className="space-y-5"
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          transition={{ delay: 0.5 }}
        >
          <div>
            <label className="block text-gray-300 font-medium mb-1">
              Correo electrónico
            </label>
            <input
              type="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              className="w-full px-4 py-2 bg-neutral-800 border border-neutral-700 rounded-lg text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="ejemplo@correo.com"
              required
              disabled={loading}
            />
          </div>

          <div>
            <label className="block text-gray-300 font-medium mb-1">
              Contraseña
            </label>
            <input
              type="password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              className="w-full px-4 py-2 bg-neutral-800 border border-neutral-700 rounded-lg text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Mínimo 6 caracteres"
              required
              disabled={loading}
              minLength={6}
            />
          </div>

          <motion.button
            type="submit"
            whileHover={{ scale: loading ? 1 : 1.05 }}
            whileTap={{ scale: loading ? 1 : 0.95 }}
            disabled={loading}
            className={`w-full ${
              loading ? 'bg-gray-600 cursor-not-allowed' : 'bg-lime-600 hover:bg-lime-500'
            } text-black font-semibold py-2 rounded-lg transition`}
          >
            {loading ? 'Iniciando sesión...' : 'Iniciar sesión'}
          </motion.button>

          <p className="text-center text-sm text-gray-400">
            ¿No tienes cuenta?{" "}
            <button 
              onClick={onRegisterClick}
              className="text-blue-500 hover:underline"
              disabled={loading}
            >
              Regístrate
            </button>
          </p>

          <p className="text-center text-sm text-gray-400 mt-3">
            <button 
              onClick={onForgotPasswordClick}
              className="text-blue-500 hover:underline"
              disabled={loading}
            >
              ¿Olvidaste tu contraseña?
            </button>
          </p>
        </motion.form>
      </motion.div>
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
                  onLogin(userData);
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
    </motion.div>
  );
}