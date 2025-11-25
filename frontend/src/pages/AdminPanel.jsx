// src/pages/AdminPanel.jsx
import { useState, useEffect } from "react";
import { ArrowLeft, Plus, Edit, Trash2 } from "lucide-react";
import Select from "react-select";
import axios from "axios";

const API = import.meta.env.VITE_API;

export default function AdminPanel({
  onBack,
  setPopup,
  setLoading
}) {
  const [editingProduct, setEditingProduct] = useState(null);
  const [products, setProducts] = useState([]);
  const [categorias, setCategorias] = useState([]);
  const [sabores, setSabores] = useState([]);
  const [selectedCategoria, setSelectedCategorias] = useState([]);
  const [selectedSabores, setSelectedSabores] = useState([]);
  const [productImages, setProductImages] = useState([]);
  const [selectedExistingImage, setSelectedExistingImage ] = useState([]);
  const [useExistingImg, setUseExistingImag] = useState(true);
  const [selectedImageFile, setSelectedImageFile] = useState(null);
  const [showAddForm, setShowAddForm] = useState(false);
  const [idProduct, setIdProduct] = useState([]);
  const [newProduct, setNewProduct] = useState({
    nombre_Producto: "",
    descripcion_Producto: "",
    categoria_Producto: "",
    precio_Producto: "",
    stock_Producto: "",
    imagen_Producto: "",
  });

  const getProducts = async () => {
    try {
      const { data } = await axios.get(`${API}/productos`);
      setProducts(data);
      const imageOptions = extractProductImages(data);
      setProductImages(imageOptions);
       
    } catch (err) {
      console.log("Error cargando productos", err);
    }
  };

  const extractProductImages = (products) => {
  const imageSet = new Set(products.map(p => p.imagen_Producto));
  return Array.from(imageSet).map(img => ({
    value: img,
    label: img,
    image: `/src/assets/products/${img}` 
  }));
};

  const getCategorias = async () => {
    const { data }  = await axios.get(`${API}/categorias`);
    setCategorias(data.map(c => ({
      value: c.id_Categoria,
      label: c.nombre_Categoria
    })));
  };

  const getSabores = async () => {
    const { data }  = await axios.get(`${API}/sabores`);
    setSabores(data.map(s => ({
      value: s.id_Sabor,
      label: s.nombre_Sabor
    })));
  };

  useEffect(() => {
    getCategorias();
    getSabores();
    getProducts();
  }, []);

  const handleAddProduct = async (e) => {
    e.preventDefault();
    setLoading(true);
    try {
      const formData = new FormData();  
      formData.append("imagen", selectedImageFile);
      formData.append("producto", JSON.stringify({
        ...newProduct,
        sabores: selectedSabores.map(s => s.value).join(","),
        categoria_Producto: selectedCategoria.value
      }));

      const {data} = await axios.post(`${API}/productos`, formData, {
        headers: { "Content-Type": "multipart/form-data" },
      });

      setPopup({
          status: data.status,
          mensaje: data.mensaje
      });
      
    } catch (err) {
      console.log("Error:", err);
    }finally {
      getProducts();
      setShowAddForm(false);
      setLoading(false);
    }
  };

  const handleUpdateProduct = async (e) => {
      e.preventDefault();
      setLoading(true);
      try {
        const formData = new FormData();
        
        formData.append("producto", JSON.stringify({
          ...editingProduct,
          sabores: selectedSabores.map(s => s.value).join(","),
          categoria_Producto: selectedCategoria.value,
          imagen_Producto: useExistingImg ? selectedExistingImage.value : null
        }));
          
        if (!useExistingImg && selectedImageFile) {
          formData.append("imagen", selectedImageFile);
        };

      const { data } = await axios.post(`${API}/editarProductos`, formData, {
        headers: { "Content-Type": "multipart/form-data" }
      });

      setPopup({
          status: data.status,
          mensaje: data.mensaje
      });

    } catch (err) {
      console.log(err);
      
      alert("Error actualizando producto");
    } finally {
      getProducts();
      setEditingProduct(null);
      setLoading(false);
    }
  };

   const onDeleteProduct = async (e, idProducto ) => {   
    e.preventDefault();
    const confirmado = window.confirm("¿Estás seguro de que deseas eliminar este producto?");
    if (!confirmado) return;
    setLoading(true);
    try {
      const { data } = await axios.delete(`${API}/productos`, {
          data: { idProducto }
        }
      );

      setPopup({
          status: data.status,
          mensaje: data.mensaje
      });
      
    } catch (err) {
      console.log("Error: ", err);
    }finally {
      getProducts();
      setShowAddForm(false);
      setLoading(false);
    }
   };

  const handleImageSelect = (e) => {
    const file = e.target.files[0];
    setSelectedImageFile(file);
    setNewProduct({ ...newProduct, imagen_Producto: file.name });
  };

  const customSelectStyles = {
    control: (base, state) => ({
      ...base,
      backgroundColor: "#262626",
      border: state.isFocused ? "2px solid #84ff00" : "1px solid #84ff00",
      boxShadow: state.isFocused ? "0 0 8px #84ff00" : "none",
      borderRadius: "6px",
      cursor: "pointer",
      ":hover": {
        borderColor: "#b4ff3d"
      },
    }),
    menu: (base) => ({
      ...base,
      backgroundColor: "#262626",
      border: "1px solid #84ff00",
      boxShadow: "0 0 12px #84ff00",
    }),
    option: (base, state) => ({
      ...base,
      backgroundColor: state.isFocused
        ? "#84ff00"
        : state.isSelected
        ? "#5fb800"
        : "#262626",
      color: state.isFocused || state.isSelected ? "#000" : "#fff",
      cursor: "pointer",
      ":active": {
        backgroundColor: "#84ff00",
        color: "#000",
      }
    }),
    singleValue: (base) => ({
      ...base,
      color: "#ffffff",
    }),
    multiValue: (base) => ({
      ...base,
      backgroundColor: "#84ff00",
      color: "#000",
    }),
    multiValueLabel: (base) => ({
      ...base,
      color: "#000"
    }),
    multiValueRemove: (base) => ({
      ...base,
      color: "#000",
      ":hover": {
        backgroundColor: "#000",
        color: "#84ff00",
      }
    }),
  };

  const formatCOP = (value) => {
    if (!value) return "";
    return "$ " + Number(value).toLocaleString("es-CO");
  };

  const parseNumber = (value) => {
    return value.replace(/[^0-9]/g, "");
  };

  const formatPrice = (price) => {
    return new Intl.NumberFormat("es-CO", {
      style: "currency",
      currency: "COP",
    }).format(price);
  };

  return (
    <div className="min-h-screen bg-neutral-900 text-white p-6">
      <div className="flex items-center justify-between mb-8">
        <button
          onClick={onBack}
          className="flex items-center gap-2 text-lime-400 hover:text-lime-300 transition"
        >
          <ArrowLeft size={20} />
          Volver al Inicio
        </button>
        <h1 className="text-3xl font-bold">Panel de Administración</h1>
        <button
          onClick={() => setShowAddForm(true)}
          className="flex items-center gap-2 bg-lime-600 hover:bg-lime-500 px-4 py-2 rounded-lg transition"
        >
          <Plus size={20} />
          Agregar Producto
        </button>
      </div>

      {showAddForm && (
        <div className="fixed inset-0 bg-black/60 flex justify-center items-center z-50">
          <div className="bg-neutral-900 p-7 rounded-xl w-full max-w-lg text-white border border-lime-500 shadow-[0_0_20px_#84ff00]">
            <h2 className="text-2xl font-bold mb-6 text-lime-400 text-center">
              Agregar Producto
            </h2>

            <form className="space-y-5" onSubmit={handleAddProduct}>
              <label className="flex flex-col gap-2">
                <span className="font-semibold text-lime-300">Nombre del producto</span>
                <input
                  type="text"
                  value={newProduct.nombre_Producto}
                  onChange={e => setNewProduct({ ...newProduct, nombre_Producto: e.target.value })}
                  className="p-2 bg-neutral-800 border border-lime-500 rounded focus:ring-2 focus:ring-lime-400"
                  required
                />
              </label>

              <label className="flex flex-col gap-2">
                <span className="font-semibold text-lime-300">Descripción</span>
                <textarea
                  value={newProduct.descripcion_Producto}
                  onChange={e => setNewProduct({ ...newProduct, descripcion_Producto: e.target.value })}
                  className="p-2 bg-neutral-800 border border-lime-500 rounded focus:ring-2 focus:ring-lime-400"
                  required
                />
              </label>

              <label className="flex flex-col gap-2">
                <span className="font-semibold text-lime-300">Categoría</span>
                <Select
                  options={categorias}
                  placeholder="Seleccionar categoría"
                  onChange={setSelectedCategorias}
                  value={selectedCategoria}
                  className="text-black"
                  styles={customSelectStyles}
                />
              </label>

              <label className="flex flex-col gap-2">
                <span className="font-semibold text-lime-300">Sabores</span>
                <Select
                  options={sabores}
                  isMulti
                  placeholder="Seleccionar sabores"
                  value={selectedSabores}
                  onChange={setSelectedSabores}
                  className="text-black"
                  styles={customSelectStyles}
                />
              </label>

              <label className="flex flex-col gap-2">
                <span className="font-semibold text-lime-300">Precio</span>
                <div className="flex items-center bg-neutral-800 border border-lime-500 rounded px-2 focus-within:ring-2 focus-within:ring-lime-400">
                  <input
                    type="text"
                    inputMode="numeric"
                    value={formatCOP(newProduct.precio_Producto)}
                    onChange={e => {
                      const numericValue = parseNumber(e.target.value);
                      setNewProduct({ ...newProduct, precio_Producto: numericValue });
                    }}
                    placeholder="$ COP"
                    className="p-2 bg-transparent outline-none w-full text-white font-semibold"
                    required
                  />
                </div>
            </label>
  
             <label className="flex flex-col gap-2">
                <span className="font-semibold text-lime-300">Stock</span>
                <input
                  type="number"
                  value={newProduct.stock_Producto}
                  onChange={e => setNewProduct({ ...newProduct, stock_Producto: e.target.value })}
                  className="p-2 bg-neutral-800 border border-lime-500 rounded focus:ring-2 focus:ring-lime-400"
                  required
                />
              </label>

              <label className="flex flex-col gap-2">
                <span className="font-semibold text-lime-300">Imagen</span>
                <input
                  type="file"
                  accept="image/*"
                  onChange={handleImageSelect}
                  className="p-2 bg-neutral-800 border border-lime-500 rounded focus:ring-2 focus:ring-lime-400"
                  required
                />
              </label>

              <div className="flex gap-3 pt-2">
                <button
                  type="submit"
                  className="flex-1 bg-lime-500 hover:bg-lime-400 text-black font-semibold py-2 rounded"
                >
                  Guardar
                </button>
                <button
                  type="button"
                  onClick={() => setShowAddForm(false)}
                  className="flex-1 bg-neutral-600 hover:bg-neutral-500 py-2 rounded"
                >
                  Cancelar
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      {editingProduct && (
        <div className="fixed inset-0 bg-black/60 flex justify-center items-center z-50">
          <div className="bg-neutral-900 p-7 rounded-xl w-full max-w-lg text-white border border-lime-500 shadow-[0_0_20px_#84ff00]">
            <h2 className="text-2xl font-bold mb-6 text-lime-400 text-center">
              Actualizar Producto
            </h2>

            <form className="space-y-5" onSubmit={handleUpdateProduct}>
              <label className="flex flex-col gap-2">
                <span className="font-semibold text-lime-300">Nombre del producto</span>
                <input
                  type="text"
                  value={editingProduct.nombre_Producto}
                  onChange={e => setEditingProduct({ ...editingProduct, nombre_Producto: e.target.value })}
                  className="p-2 bg-neutral-800 border border-lime-500 rounded focus:ring-2 focus:ring-lime-400"
                  required
                />
              </label>

              <label className="flex flex-col gap-2">
                <span className="font-semibold text-lime-300">Descripción</span>
                <textarea
                  value={editingProduct.descripcion_Producto}
                  onChange={e => setEditingProduct({ ...editingProduct, descripcion_Producto: e.target.value })}
                  className="p-2 bg-neutral-800 border border-lime-500 rounded focus:ring-2 focus:ring-lime-400"
                  required
                />
              </label>

              <label className="flex items-center gap-2 text-lime-300 font-semibold mt-4">
                <input type="radio" checked={useExistingImg} onChange={() => setUseExistingImag(true)} />
                  Usar imagen existente
              </label>

              {useExistingImg && (
              <Select
                options={productImages}
                value={selectedExistingImage}
                onChange={setSelectedExistingImage}
                placeholder="Seleccionar imagen existente"
                styles={customSelectStyles}
                formatOptionLabel={opt => (
                  <div style={{ display: "flex", alignItems: "center", gap: 8 }}>
                    <img 
                      src={opt.image} 
                      width={40} 
                      height={40} 
                      style={{ borderRadius: 6 }} 
                      onError={(e) => {
                      e.target.onerror = null;
                      e.target.src = "/src/assets/products/default.png";
                    }}/>
                    <span>{opt.label}</span>
                  </div>
                )}
              />
            )}

            <label className="flex items-center gap-2 text-lime-300 font-semibold mt-4">
              <input type="radio" checked={!useExistingImg} onChange={() => setUseExistingImag(false)} />
              Subir nueva imagen
            </label>

            {!useExistingImg && (
              <input
                type="file"
                accept="image/*"
                onChange={handleImageSelect}
                className="p-2 bg-neutral-800 border border-lime-500 rounded focus:ring-2 focus:ring-lime-400"
                required
              />
            )}

              <label className="flex flex-col gap-2">
                <span className="font-semibold text-lime-300">Categoría</span>
                <Select
                  options={categorias}
                  placeholder="Seleccionar categoría"
                  onChange={setSelectedCategorias}
                  value={selectedCategoria}
                  className="text-black"
                  styles={customSelectStyles}
                />
              </label>

              <label className="flex flex-col gap-2">
                <span className="font-semibold text-lime-300">Sabores</span>
                <Select
                  options={sabores}
                  isMulti
                  placeholder="Seleccionar sabores"
                  value={selectedSabores}
                  onChange={setSelectedSabores}
                  className="text-black"
                  styles={customSelectStyles}
                />
              </label>

              <label className="flex flex-col gap-2">
                <span className="font-semibold text-lime-300">Precio</span>
                <div className="flex items-center bg-neutral-800 border border-lime-500 rounded px-2 focus-within:ring-2 focus-within:ring-lime-400">
                  <input
                    type="text"
                    inputMode="numeric"
                    value={formatCOP(editingProduct.precio_Producto)}
                    onChange={e => {
                      const numericValue = parseNumber(e.target.value);
                      setEditingProduct({ ...editingProduct, precio_Producto: numericValue });
                    }}
                    placeholder="$ COP"
                    className="p-2 bg-transparent outline-none w-full text-white font-semibold"
                    required
                  />
                </div>
            </label>
  
             <label className="flex flex-col gap-2">
                <span className="font-semibold text-lime-300">Stock</span>
                <input
                  type="number"
                  value={editingProduct.stock_Producto}
                  onChange={e => setEditingProduct({ ...editingProduct, stock_Producto: e.target.value })}
                  className="p-2 bg-neutral-800 border border-lime-500 rounded focus:ring-2 focus:ring-lime-400"
                  required
                />
            </label>
            
              <div className="flex gap-3 pt-2">
                <button
                  type="submit"
                  className="flex-1 bg-lime-500 hover:bg-lime-400 text-black font-semibold py-2 rounded"
                >
                  Guardar
                </button>
                <button
                  type="button"
                  onClick={() => setEditingProduct(false)}
                  className="flex-1 bg-neutral-600 hover:bg-neutral-500 py-2 rounded"
                >
                  Cancelar
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {products.map((product) => {
          const imgSrc = `/src/assets/products/${product.imagen_Producto}`;

          return (
            <div
              key={product.id_Producto}
              className="bg-neutral-800 rounded-xl p-4 shadow-lg border border-neutral-700 hover:border-lime-400 transition"
            >
              <img
                src={imgSrc}
                alt={product.nombre_Producto}
                className="w-full h-40 object-cover rounded-md mb-3"
                onError={(e) => {
                  e.target.onerror = null;
                  e.target.src = "/src/assets/products/default.png";
                }}
              />

              <h3 className="text-xl font-semibold text-white mb-1 truncate">
                {product.nombre_Producto}
              </h3>

              <p className="text-sm text-neutral-400 mb-1">
                Categoría: <span className="text-lime-400 font-medium">{product.categoria_Producto}</span>
              </p>

              <p className="text-sm text-neutral-400 mb-1">
                Stock: <span className="text-white font-medium">{product.stock_Producto}</span>
              </p>

              <div className="text-sm text-neutral-300 mb-2 overflow-hidden">
                <span className="font-medium text-neutral-100">Sabores: </span>
                <span className="text-lime-300  font-medium">
                  {product.sabores?.map(s => s.nombre_Sabor).join(", ")}
                </span>
              </div>

              <p className="text-lime-400 text-xl font-bold mb-3">
                {formatPrice(product.precio_Producto)}
              </p>

              <div className="grid grid-cols-2 gap-2">
                <button
                  onClick={() => {
                    setEditingProduct(product);
                    setUseExistingImag(true);

                    const img = productImages.find(p => p.value === product.imagen_Producto);
                    setSelectedExistingImage(img || null);

                    const cat = categorias.find(c => c.label === product.categoria_Producto);
                    setSelectedCategorias(cat || null);

                    const sabs = sabores.filter(s =>
                      product.sabores.some(ps => ps.id_Sabor === s.value)
                    );
                    setSelectedSabores(sabs || []);
                  }}
                  className="bg-blue-600 hover:bg-blue-500 py-2 rounded flex items-center justify-center gap-1 font-medium"
                >
                  <Edit size={16} />
                  Editar
                </button>
                <button
                  onClick={(e) => onDeleteProduct(e, product.id_Producto)}
                  className="bg-red-600 hover:bg-red-500 py-2 rounded flex items-center justify-center gap-1 font-medium"
                >
                  <Trash2 size={16} />
                  Eliminar
                </button>
              </div>
            </div>
          );
        })}
      </div>

      {products.length === 0 && (
        <div className="text-center py-12">
          <p className="text-neutral-400 text-lg">
            No hay productos registrados
          </p>
        </div>
      )}
    </div>
  );
}
