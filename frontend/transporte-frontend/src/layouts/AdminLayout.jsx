import { Link, useLocation, useNavigate } from 'react-router-dom'
import { ChevronDownIcon, UserCircleIcon } from '@heroicons/react/24/outline'
import { useState } from 'react'

const navLinks = [
  { label: 'Dashboard', path: '/admin/dashboard' },
  { label: 'Veículos', path: '/admin/veiculos' },
  { label: 'Manutenções', path: '/admin/manutencoes' },
  { label: 'Viagens', path: '/admin/viagens' },
  { label: 'Usuários', path: '/admin/usuarios' },
]

export default function AdminLayout({ children }) {
  const location = useLocation()
  const navigate = useNavigate()
  const [dropdownOpen, setDropdownOpen] = useState(false)

  return (
    <div className="min-h-screen flex flex-col bg-gray-50">

      {/* NAVBAR */}
      <nav className="bg-[#1a3a5c] text-white px-8 py-4 flex items-center justify-between shadow-lg w-full">

        {/* Logo */}
        <div className="flex items-center gap-3">
          <div className="bg-white/10 rounded-lg p-2">
            <svg className="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
              <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99z"/>
            </svg>
          </div>
          <div>
            <span className="text-2xl font-extrabold tracking-wide">VIAGE</span>
            <p className="text-xs text-blue-200 -mt-1">saúde</p>
          </div>
        </div>

        {/* Links */}
        <div className="flex items-center gap-8">
          {navLinks.map(link => (
            <Link
              key={link.path}
              to={link.path}
              className={`text-sm font-semibold transition-all hover:text-blue-200 pb-1 ${
                location.pathname === link.path
                  ? 'border-b-2 border-white text-white'
                  : 'text-blue-100'
              }`}
            >
              {link.label}
            </Link>
          ))}
        </div>

        {/* User Menu */}
        <div className="relative">
          <button
            onClick={() => setDropdownOpen(!dropdownOpen)}
            className="flex items-center gap-2 hover:bg-white/10 rounded-lg px-3 py-2 transition"
          >
            <ChevronDownIcon className="w-4 h-4" />
            <span className="text-sm font-medium">Olá, ADMIN</span>
            <div className="w-9 h-9 rounded-full bg-gray-300 flex items-center justify-center">
              <UserCircleIcon className="w-7 h-7 text-gray-600" />
            </div>
          </button>

          {dropdownOpen && (
            <div className="absolute right-0 mt-2 w-44 bg-white rounded-xl shadow-lg z-50 text-gray-700 overflow-hidden">
              <Link to="/admin/perfil" className="block px-4 py-3 text-sm hover:bg-gray-50">
                Meu Perfil
              </Link>
              <hr />
              <button
                onClick={() => navigate('/logout')}
                className="w-full text-left px-4 py-3 text-sm text-red-500 hover:bg-red-50"
              >
                Sair
              </button>
            </div>
          )}
        </div>
      </nav>

      {/* CONTENT */}
      <main className="flex-1 px-8 py-8 w-full">
        {children}
      </main>

      {/* FOOTER */}
      <footer className="bg-[#1a3a5c] h-16" />
    </div>
  )
}