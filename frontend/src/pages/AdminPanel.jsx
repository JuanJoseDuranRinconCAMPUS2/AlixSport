// src/pages/AdminPanel.jsx
import { useState } from "react";
import { ArrowLeft, Plus, Edit, Trash2 } from "lucide-react";

export default function AdminPanel({ products, onAddProduct, onUpdateProduct, onDeleteProduct, onBack }) {
  const [editingProduct, setEditingProduct] = useState(null);
  const [showAddForm, setShowAddForm] = useState(false);
  const [newProduct, setNewProduct] = useState({
    name: "",
    price: "",
    image: ""
  });

  const handleAddProduct = (e) => {
    e.preventDefault();
    onAddProduct({
      ...newProduct,
      price: parseInt(newProduct.price)
    });
    setNewProduct({ name: "", price: "", image: "" });
    setShowAddForm(false);
  };

  const handleUpdateProduct = (e) => {
    e.preventDefault();
    onUpdateProduct(editingProduct);
    setEditingProduct(null);
  };

  const formatPrice = (price) => {
    return new Intl.NumberFormat('es-CO', {
      style: 'currency',
      currency: 'COP'
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
        <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
          <div className="bg-neutral-800 p-6 rounded-lg w-full max-w-md">
            <h2 className="text-xl font-bold mb-4">Agregar Nuevo Producto</h2>
            <form onSubmit={handleAddProduct} className="space-y-4">
              <input
                type="text"
                placeholder="Nombre del producto"
                value={newProduct.name}
                onChange={(e) => setNewProduct({...newProduct, name: e.target.value})}
                className="w-full p-2 bg-neutral-700 rounded border border-neutral-600"
                required
              />
              <input
                type="number"
                placeholder="Precio"
                value={newProduct.price}
                onChange={(e) => setNewProduct({...newProduct, price: e.target.value})}
                className="w-full p-2 bg-neutral-700 rounded border border-neutral-600"
                required
              />
              <input
                type="text"
                placeholder="URL de la imagen"
                value={newProduct.image}
                onChange={(e) => setNewProduct({...newProduct, image: e.target.value})}
                className="w-full p-2 bg-neutral-700 rounded border border-neutral-600"
                required
              />
              <div className="flex gap-2">
                <button type="submit" className="flex-1 bg-lime-600 hover:bg-lime-500 py-2 rounded">
                  Guardar
                </button>
                <button type="button" onClick={() => setShowAddForm(false)} className="flex-1 bg-neutral-600 hover:bg-neutral-500 py-2 rounded">
                  Cancelar
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      {editingProduct && (
        <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
          <div className="bg-neutral-800 p-6 rounded-lg w-full max-w-md">
            <h2 className="text-xl font-bold mb-4">Editar Producto</h2>
            <form onSubmit={handleUpdateProduct} className="space-y-4">
              <input
                type="text"
                value={editingProduct.name}
                onChange={(e) => setEditingProduct({...editingProduct, name: e.target.value})}
                className="w-full p-2 bg-neutral-700 rounded border border-neutral-600"
                required
              />
              <input
                type="number"
                value={editingProduct.price}
                onChange={(e) => setEditingProduct({...editingProduct, price: parseInt(e.target.value)})}
                className="w-full p-2 bg-neutral-700 rounded border border-neutral-600"
                required
              />
              <input
                type="text"
                value={editingProduct.image}
                onChange={(e) => setEditingProduct({...editingProduct, image: e.target.value})}
                className="w-full p-2 bg-neutral-700 rounded border border-neutral-600"
                required
              />
              <div className="flex gap-2">
                <button type="submit" className="flex-1 bg-lime-600 hover:bg-lime-500 py-2 rounded">
                  Actualizar
                </button>
                <button type="button" onClick={() => setEditingProduct(null)} className="flex-1 bg-neutral-600 hover:bg-neutral-500 py-2 rounded">
                  Cancelar
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {products.map((product) => (
          <div key={product.id} className="bg-neutral-800 rounded-lg p-4">
            <img
              src={product.image}
              alt={product.name}
              className="w-full h-48 object-cover rounded-lg mb-3"
            />
            <h3 className="text-lg font-semibold">{product.name}</h3>
            <p className="text-lime-400 font-bold">{formatPrice(product.price)}</p>
            <div className="flex gap-2 mt-3">
              <button
                onClick={() => setEditingProduct(product)}
                className="flex-1 bg-blue-600 hover:bg-blue-500 py-2 rounded flex items-center justify-center gap-1"
              >
                <Edit size={16} />
                Editar
              </button>
              <button
                onClick={() => onDeleteProduct(product.id)}
                className="flex-1 bg-red-600 hover:bg-red-500 py-2 rounded flex items-center justify-center gap-1"
              >
                <Trash2 size={16} />
                Eliminar
              </button>
            </div>
          </div>
        ))}
      </div>

      {products.length === 0 && (
        <div className="text-center py-12">
          <p className="text-neutral-400 text-lg">No hay productos registrados</p>
        </div>
      )}
    </div>
  );
}