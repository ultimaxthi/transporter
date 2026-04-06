import { useState } from 'react'
import {
  PlusIcon,
  MagnifyingGlassIcon,
  UserCircleIcon,
  TruckIcon,
  ClipboardDocumentListIcon,
  CheckCircleIcon,
  XCircleIcon,
  PencilSquareIcon,
  TrashIcon,
  EyeIcon,
} from '@heroicons/react/24/outline'
import AdminLayout from '../../layouts/AdminLayout'

// ─── Dados fictícios ──────────────────────────────────────────────────────────
const mockUsers = [
  {
    id: 1,
    name: 'Carlos Eduardo Souza',
    email: 'carlos@viage.com',
    role: 'driver',
    registration_number: 'MOT-001',
    active: true,
    activeTrip: { destination_neighborhood: 'Vila Nova' },
    activeVehicle: { plate: 'ABC-1234', model: 'Corolla' },
  },
  {
    id: 2,
    name: 'Marcos Antônio Lima',
    email: 'marcos@viage.com',
    role: 'driver',
    registration_number: 'MOT-002',
    active: true,
    activeTrip: null,
    activeVehicle: { plate: 'DEF-5678', model: 'Strada' },
  },
  {
    id: 3,
    name: 'Roberto Silva',
    email: 'roberto@viage.com',
    role: 'driver',
    registration_number: 'MOT-003',
    active: false,
    activeTrip: null,
    activeVehicle: null,
  },
  {
    id: 4,
    name: 'Ana Paula Ferreira',
    email: 'ana@viage.com',
    role: 'operator',
    registration_number: 'OP-001',
    active: true,
    tripsCreated: 34,
  },
  {
    id: 5,
    name: 'João Pedro Martins',
    email: 'joao@viage.com',
    role: 'operator',
    registration_number: 'OP-002',
    active: true,
    tripsCreated: 21,
  },
  {
    id: 6,
    name: 'Fernanda Costa',
    email: 'fernanda@viage.com',
    role: 'operator',
    registration_number: 'OP-003',
    active: false,
    tripsCreated: 8,
  },
]

// ─── Componentes auxiliares ───────────────────────────────────────────────────

function StatusBadge({ active }) {
  return active ? (
    <span className="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
      <CheckCircleIcon className="w-3.5 h-3.5" />
      Ativo
    </span>
  ) : (
    <span className="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-600">
      <XCircleIcon className="w-3.5 h-3.5" />
      Inativo
    </span>
  )
}

function DriverStatusBadge({ user }) {
  if (!user.active) {
    return (
      <span className="px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">
        Inativo
      </span>
    )
  }
  if (user.activeTrip) {
    return (
      <span className="px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
        Em Viagem
      </span>
    )
  }
  if (user.activeVehicle) {
    return (
      <span className="px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
        Disponível
      </span>
    )
  }
  return (
    <span className="px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
      Sem Veículo
    </span>
  )
}

// ─── Resumo em cards ──────────────────────────────────────────────────────────
function SummaryCards({ users, activeTab }) {
  const filtered = users.filter(u => u.role === activeTab)
  const total = filtered.length
  const ativos = filtered.filter(u => u.active).length
  const inativos = filtered.filter(u => !u.active).length
  const emViagem = filtered.filter(u => u.activeTrip).length

  const cards =
    activeTab === 'driver'
      ? [
          { label: 'Total de Motoristas', value: total, color: 'bg-[#1a3a5c]', text: 'text-white' },
          { label: 'Ativos', value: ativos, color: 'bg-green-50', text: 'text-green-700', border: 'border border-green-200' },
          { label: 'Em Viagem', value: emViagem, color: 'bg-blue-50', text: 'text-blue-700', border: 'border border-blue-200' },
          { label: 'Inativos', value: inativos, color: 'bg-red-50', text: 'text-red-600', border: 'border border-red-200' },
        ]
      : [
          { label: 'Total de Operadores', value: total, color: 'bg-[#1a3a5c]', text: 'text-white' },
          { label: 'Ativos', value: ativos, color: 'bg-green-50', text: 'text-green-700', border: 'border border-green-200' },
          { label: 'Inativos', value: inativos, color: 'bg-red-50', text: 'text-red-600', border: 'border border-red-200' },
        ]

  return (
    <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
      {cards.map(card => (
        <div
          key={card.label}
          className={`rounded-2xl p-4 flex flex-col gap-1 shadow-sm ${card.color} ${card.border ?? ''}`}
        >
          <p className={`text-sm font-medium ${card.text} opacity-80`}>{card.label}</p>
          <p className={`text-3xl font-extrabold ${card.text}`}>{card.value}</p>
        </div>
      ))}
    </div>
  )
}

// ─── Tabela de motoristas ─────────────────────────────────────────────────────
function DriversTable({ users, onEdit, onToggle, onDelete }) {
  const [search, setSearch] = useState('')

  const filtered = users
    .filter(u => u.role === 'driver')
    .filter(u =>
      u.name.toLowerCase().includes(search.toLowerCase()) ||
      u.registration_number.toLowerCase().includes(search.toLowerCase())
    )

  return (
    <div className="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
      {/* Header da tabela */}
      <div className="bg-gray-100 px-5 py-3 border-b border-gray-200 flex items-center justify-between">
        <div className="flex items-center gap-2">
          <TruckIcon className="w-5 h-5 text-[#1a3a5c]" />
          <h2 className="font-bold text-gray-800">Motoristas</h2>
        </div>
        <div className="relative">
          <MagnifyingGlassIcon className="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
          <input
            type="text"
            placeholder="Buscar motorista..."
            value={search}
            onChange={e => setSearch(e.target.value)}
            className="pl-9 pr-4 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1a3a5c] w-56"
          />
        </div>
      </div>

      <div className="overflow-x-auto">
        <table className="w-full text-sm">
          <thead>
            <tr className="text-left text-gray-500 border-b border-gray-100 bg-gray-50">
              <th className="px-5 py-3 font-semibold">Motorista</th>
              <th className="px-5 py-3 font-semibold">Matrícula</th>
              <th className="px-5 py-3 font-semibold">Veículo Atribuído</th>
              <th className="px-5 py-3 font-semibold">Situação</th>
              <th className="px-5 py-3 font-semibold">Status</th>
              <th className="px-5 py-3 font-semibold">Ações</th>
            </tr>
          </thead>
          <tbody>
            {filtered.length === 0 ? (
              <tr>
                <td colSpan={6} className="text-center py-12 text-gray-400">
                  Nenhum motorista encontrado.
                </td>
              </tr>
            ) : (
              filtered.map(user => (
                <tr
                  key={user.id}
                  className={`border-b border-gray-50 transition hover:bg-gray-50 ${!user.active ? 'opacity-60' : ''}`}
                >
                  {/* Nome + Email */}
                  <td className="px-5 py-3">
                    <div className="flex items-center gap-3">
                      <div className="w-9 h-9 rounded-full bg-[#1a3a5c]/10 flex items-center justify-center text-[#1a3a5c] font-bold text-sm">
                        {user.name.charAt(0)}
                      </div>
                      <div>
                        <p className="font-semibold text-gray-800">{user.name}</p>
                        <p className="text-xs text-gray-400">{user.email}</p>
                      </div>
                    </div>
                  </td>

                  {/* Matrícula */}
                  <td className="px-5 py-3">
                    <span className="font-mono text-xs bg-gray-100 px-2 py-1 rounded">
                      {user.registration_number}
                    </span>
                  </td>

                  {/* Veículo */}
                  <td className="px-5 py-3">
                    {user.activeVehicle ? (
                      <div>
                        <p className="font-mono font-semibold text-gray-700">
                          {user.activeVehicle.plate}
                        </p>
                        <p className="text-xs text-gray-400">{user.activeVehicle.model}</p>
                      </div>
                    ) : (
                      <span className="text-gray-400 italic text-xs">Sem veículo</span>
                    )}
                  </td>

                  {/* Situação */}
                  <td className="px-5 py-3">
                    <DriverStatusBadge user={user} />
                  </td>

                  {/* Ativo/Inativo */}
                  <td className="px-5 py-3">
                    <StatusBadge active={user.active} />
                  </td>

                  {/* Ações */}
                  <td className="px-5 py-3">
                    <div className="flex items-center gap-2">
                      <button
                        onClick={() => onEdit(user)}
                        className="p-1.5 rounded-lg text-blue-500 hover:bg-blue-50 transition"
                        title="Editar"
                      >
                        <PencilSquareIcon className="w-4 h-4" />
                      </button>
                      <button
                        onClick={() => onToggle(user)}
                        className={`p-1.5 rounded-lg transition ${
                          user.active
                            ? 'text-orange-500 hover:bg-orange-50'
                            : 'text-green-500 hover:bg-green-50'
                        }`}
                        title={user.active ? 'Desativar' : 'Ativar'}
                      >
                        {user.active
                          ? <XCircleIcon className="w-4 h-4" />
                          : <CheckCircleIcon className="w-4 h-4" />
                        }
                      </button>
                      <button
                        onClick={() => onDelete(user)}
                        className="p-1.5 rounded-lg text-red-500 hover:bg-red-50 transition"
                        title="Remover"
                      >
                        <TrashIcon className="w-4 h-4" />
                      </button>
                    </div>
                  </td>
                </tr>
              ))
            )}
          </tbody>
        </table>
      </div>
    </div>
  )
}

// ─── Tabela de operadores ─────────────────────────────────────────────────────
function OperatorsTable({ users, onEdit, onToggle, onDelete }) {
  const [search, setSearch] = useState('')

  const filtered = users
    .filter(u => u.role === 'operator')
    .filter(u =>
      u.name.toLowerCase().includes(search.toLowerCase()) ||
      u.registration_number.toLowerCase().includes(search.toLowerCase())
    )

  return (
    <div className="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
      {/* Header */}
      <div className="bg-gray-100 px-5 py-3 border-b border-gray-200 flex items-center justify-between">
        <div className="flex items-center gap-2">
          <ClipboardDocumentListIcon className="w-5 h-5 text-[#1a3a5c]" />
          <h2 className="font-bold text-gray-800">Operadores</h2>
        </div>
        <div className="relative">
          <MagnifyingGlassIcon className="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
          <input
            type="text"
            placeholder="Buscar operador..."
            value={search}
            onChange={e => setSearch(e.target.value)}
            className="pl-9 pr-4 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1a3a5c] w-56"
          />
        </div>
      </div>

      <div className="overflow-x-auto">
        <table className="w-full text-sm">
          <thead>
            <tr className="text-left text-gray-500 border-b border-gray-100 bg-gray-50">
              <th className="px-5 py-3 font-semibold">Operador</th>
              <th className="px-5 py-3 font-semibold">Matrícula</th>
              <th className="px-5 py-3 font-semibold">Viagens Criadas</th>
              <th className="px-5 py-3 font-semibold">Status</th>
              <th className="px-5 py-3 font-semibold">Ações</th>
            </tr>
          </thead>
          <tbody>
            {filtered.length === 0 ? (
              <tr>
                <td colSpan={5} className="text-center py-12 text-gray-400">
                  Nenhum operador encontrado.
                </td>
              </tr>
            ) : (
              filtered.map(user => (
                <tr
                  key={user.id}
                  className={`border-b border-gray-50 transition hover:bg-gray-50 ${!user.active ? 'opacity-60' : ''}`}
                >
                  {/* Nome + Email */}
                  <td className="px-5 py-3">
                    <div className="flex items-center gap-3">
                      <div className="w-9 h-9 rounded-full bg-purple-100 flex items-center justify-center text-purple-700 font-bold text-sm">
                        {user.name.charAt(0)}
                      </div>
                      <div>
                        <p className="font-semibold text-gray-800">{user.name}</p>
                        <p className="text-xs text-gray-400">{user.email}</p>
                      </div>
                    </div>
                  </td>

                  {/* Matrícula */}
                  <td className="px-5 py-3">
                    <span className="font-mono text-xs bg-gray-100 px-2 py-1 rounded">
                      {user.registration_number}
                    </span>
                  </td>

                  {/* Viagens criadas */}
                  <td className="px-5 py-3">
                    <div className="flex items-center gap-2">
                      <div className="w-24 bg-gray-100 rounded-full h-2">
                        <div
                          className="bg-[#1a3a5c] h-2 rounded-full"
                          style={{ width: `${Math.min((user.tripsCreated / 50) * 100, 100)}%` }}
                        />
                      </div>
                      <span className="text-gray-700 font-semibold">{user.tripsCreated}</span>
                    </div>
                  </td>

                  {/* Status */}
                  <td className="px-5 py-3">
                    <StatusBadge active={user.active} />
                  </td>

                  {/* Ações */}
                  <td className="px-5 py-3">
                    <div className="flex items-center gap-2">
                      <button
                        onClick={() => onEdit(user)}
                        className="p-1.5 rounded-lg text-blue-500 hover:bg-blue-50 transition"
                        title="Editar"
                      >
                        <PencilSquareIcon className="w-4 h-4" />
                      </button>
                      <button
                        onClick={() => onToggle(user)}
                        className={`p-1.5 rounded-lg transition ${
                          user.active
                            ? 'text-orange-500 hover:bg-orange-50'
                            : 'text-green-500 hover:bg-green-50'
                        }`}
                        title={user.active ? 'Desativar' : 'Ativar'}
                      >
                        {user.active
                          ? <XCircleIcon className="w-4 h-4" />
                          : <CheckCircleIcon className="w-4 h-4" />
                        }
                      </button>
                      <button
                        onClick={() => onDelete(user)}
                        className="p-1.5 rounded-lg text-red-500 hover:bg-red-50 transition"
                        title="Remover"
                      >
                        <TrashIcon className="w-4 h-4" />
                      </button>
                    </div>
                  </td>
                </tr>
              ))
            )}
          </tbody>
        </table>
      </div>
    </div>
  )
}

// ─── Modal Criar/Editar Usuário ───────────────────────────────────────────────
function UserModal({ user, defaultRole, onClose, onSave }) {
  const isEdit = !!user

  const [form, setForm] = useState({
    name: user?.name ?? '',
    email: user?.email ?? '',
    password: '',
    role: user?.role ?? defaultRole ?? 'driver',
    registration_number: user?.registration_number ?? '',
    active: user?.active ?? true,
  })

  function handleChange(e) {
    const { name, value, type, checked } = e.target
    setForm(prev => ({
      ...prev,
      [name]: type === 'checkbox' ? checked : value,
    }))
  }

  return (
    <div className="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
      <div className="bg-white rounded-2xl shadow-2xl w-full max-w-lg">

        {/* Header do Modal */}
        <div className="flex items-center justify-between px-6 py-4 border-b border-gray-100">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 rounded-xl bg-[#1a3a5c]/10 flex items-center justify-center">
              <UserCircleIcon className="w-6 h-6 text-[#1a3a5c]" />
            </div>
            <div>
              <h2 className="text-lg font-bold text-gray-800">
                {isEdit ? 'Editar Usuário' : 'Novo Usuário'}
              </h2>
              <p className="text-xs text-gray-400">
                {isEdit ? `Editando: ${user.name}` : 'Preencha os dados abaixo'}
              </p>
            </div>
          </div>
          <button
            onClick={onClose}
            className="text-gray-400 hover:text-gray-600 text-xl font-bold"
          >
            ×
          </button>
        </div>

        {/* Corpo do Modal */}
        <div className="px-6 py-5 flex flex-col gap-4">

          {/* Tipo de usuário */}
          <div>
            <label className="block text-sm font-semibold text-gray-600 mb-2">
              Tipo de Usuário
            </label>
            <div className="flex gap-3">
              {[
                { value: 'driver', label: 'Motorista', icon: '🚗' },
                { value: 'operator', label: 'Operador', icon: '📋' },
                { value: 'admin', label: 'Administrador', icon: '🔧' },
              ].map(opt => (
                <button
                  key={opt.value}
                  type="button"
                  onClick={() => setForm(prev => ({ ...prev, role: opt.value }))}
                  className={`flex-1 py-2 px-3 rounded-xl border-2 text-sm font-medium transition ${
                    form.role === opt.value
                      ? 'border-[#1a3a5c] bg-[#1a3a5c] text-white'
                      : 'border-gray-200 text-gray-600 hover:border-gray-300'
                  }`}
                >
                  <span className="mr-1">{opt.icon}</span>
                  {opt.label}
                </button>
              ))}
            </div>
          </div>

          {/* Nome */}
          <div>
            <label className="block text-sm font-medium text-gray-600 mb-1">Nome completo</label>
            <input
              type="text"
              name="name"
              value={form.name}
              onChange={handleChange}
              placeholder="Ex: Carlos Eduardo Souza"
              className="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a3a5c]"
            />
          </div>

          {/* Email */}
          <div>
            <label className="block text-sm font-medium text-gray-600 mb-1">E-mail</label>
            <input
              type="email"
              name="email"
              value={form.email}
              onChange={handleChange}
              placeholder="Ex: carlos@viage.com"
              className="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a3a5c]"
            />
          </div>

          {/* Senha */}
          <div>
            <label className="block text-sm font-medium text-gray-600 mb-1">
              {isEdit ? 'Nova Senha (deixe em branco para manter)' : 'Senha'}
            </label>
            <input
              type="password"
              name="password"
              value={form.password}
              onChange={handleChange}
              placeholder={isEdit ? 'Deixe em branco para manter' : 'Mínimo 6 caracteres'}
              className="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a3a5c]"
            />
          </div>

          {/* Matrícula + Status lado a lado */}
          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-600 mb-1">Matrícula</label>
              <input
                type="text"
                name="registration_number"
                value={form.registration_number}
                onChange={handleChange}
                placeholder="Ex: MOT-001"
                className="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a3a5c]"
              />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-600 mb-2">Status</label>
              <label className="flex items-center gap-3 cursor-pointer">
                <div
                  onClick={() => setForm(prev => ({ ...prev, active: !prev.active }))}
                  className={`relative w-11 h-6 rounded-full transition-colors ${
                    form.active ? 'bg-green-500' : 'bg-gray-300'
                  }`}
                >
                  <div className={`absolute top-1 w-4 h-4 bg-white rounded-full shadow transition-transform ${
                    form.active ? 'translate-x-6' : 'translate-x-1'
                  }`} />
                </div>
                <span className={`text-sm font-medium ${form.active ? 'text-green-600' : 'text-gray-400'}`}>
                  {form.active ? 'Ativo' : 'Inativo'}
                </span>
              </label>
            </div>
          </div>
        </div>

        {/* Footer do Modal */}
        <div className="flex justify-end gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50 rounded-b-2xl">
          <button
            onClick={onClose}
            className="px-4 py-2 text-sm text-gray-600 hover:bg-gray-200 rounded-lg transition"
          >
            Cancelar
          </button>
          <button
            onClick={() => onSave(form)}
            className="px-6 py-2 bg-[#1a3a5c] text-white text-sm font-semibold rounded-lg hover:bg-[#15304d] transition shadow"
          >
            {isEdit ? 'Salvar Alterações' : 'Criar Usuário'}
          </button>
        </div>
      </div>
    </div>
  )
}

// ─── Modal de Confirmação ─────────────────────────────────────────────────────
function ConfirmModal({ title, message, confirmLabel, confirmColor, onConfirm, onClose }) {
  return (
    <div className="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
      <div className="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6">
        <h2 className="text-lg font-bold text-gray-800 mb-2">{title}</h2>
        <p className="text-sm text-gray-500 mb-6">{message}</p>
        <div className="flex justify-end gap-3">
          <button
            onClick={onClose}
            className="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg"
          >
            Cancelar
          </button>
          <button
            onClick={onConfirm}
            className={`px-5 py-2 text-sm font-semibold text-white rounded-lg transition ${confirmColor}`}
          >
            {confirmLabel}
          </button>
        </div>
      </div>
    </div>
  )
}

// ─── Página Principal ─────────────────────────────────────────────────────────
export default function Usuarios() {
  const [users, setUsers] = useState(mockUsers)
  const [activeTab, setActiveTab] = useState('driver')
  const [showModal, setShowModal] = useState(false)
  const [editingUser, setEditingUser] = useState(null)
  const [confirmModal, setConfirmModal] = useState(null)

  // Abrir modal de criação
  function handleNew() {
    setEditingUser(null)
    setShowModal(true)
  }

  // Abrir modal de edição
  function handleEdit(user) {
    setEditingUser(user)
    setShowModal(true)
  }

  // Salvar (criar ou editar)
  function handleSave(form) {
    if (editingUser) {
      setUsers(prev =>
        prev.map(u => (u.id === editingUser.id ? { ...u, ...form } : u))
      )
    } else {
      setUsers(prev => [
        ...prev,
        { id: Date.now(), ...form, tripsCreated: 0, activeTrip: null, activeVehicle: null },
      ])
    }
    setShowModal(false)
    setEditingUser(null)
  }

  // Ativar / Desativar
  function handleToggle(user) {
    setConfirmModal({
      title: user.active ? 'Desativar usuário?' : 'Ativar usuário?',
      message: user.active
        ? `O usuário "${user.name}" será desativado e não poderá acessar o sistema.`
        : `O usuário "${user.name}" será ativado novamente.`,
      confirmLabel: user.active ? 'Desativar' : 'Ativar',
      confirmColor: user.active ? 'bg-orange-500 hover:bg-orange-600' : 'bg-green-500 hover:bg-green-600',
      onConfirm: () => {
        setUsers(prev =>
          prev.map(u => (u.id === user.id ? { ...u, active: !u.active } : u))
        )
        setConfirmModal(null)
      },
    })
  }

  // Deletar
  function handleDelete(user) {
    setConfirmModal({
      title: 'Remover usuário?',
      message: `Tem certeza que deseja remover "${user.name}"? Esta ação não pode ser desfeita.`,
      confirmLabel: 'Remover',
      confirmColor: 'bg-red-500 hover:bg-red-600',
      onConfirm: () => {
        setUsers(prev => prev.filter(u => u.id !== user.id))
        setConfirmModal(null)
      },
    })
  }

  return (
    <AdminLayout>
      {/* Cabeçalho */}
      <div className="flex items-center justify-between mb-6">
        <h1 className="text-3xl font-extrabold text-gray-900">Usuários</h1>
        <button
          onClick={handleNew}
          className="flex items-center gap-2 bg-[#1a3a5c] text-white px-5 py-2.5 rounded-full font-semibold hover:bg-[#15304d] transition shadow"
        >
          <PlusIcon className="w-5 h-5 text-green-400" />
          Novo Usuário
        </button>
      </div>

      {/* Abas */}
      <div className="flex gap-2 mb-6">
        <button
          onClick={() => setActiveTab('driver')}
          className={`flex items-center gap-2 px-5 py-2.5 rounded-full text-sm font-semibold transition ${
            activeTab === 'driver'
              ? 'bg-[#1a3a5c] text-white shadow'
              : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'
          }`}
        >
          <TruckIcon className="w-4 h-4" />
          Motoristas
          <span className={`ml-1 px-2 py-0.5 rounded-full text-xs ${
            activeTab === 'driver' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-600'
          }`}>
            {users.filter(u => u.role === 'driver').length}
          </span>
        </button>

        <button
          onClick={() => setActiveTab('operator')}
          className={`flex items-center gap-2 px-5 py-2.5 rounded-full text-sm font-semibold transition ${
            activeTab === 'operator'
              ? 'bg-[#1a3a5c] text-white shadow'
              : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'
          }`}
        >
          <ClipboardDocumentListIcon className="w-4 h-4" />
          Operadores
          <span className={`ml-1 px-2 py-0.5 rounded-full text-xs ${
            activeTab === 'operator' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-600'
          }`}>
            {users.filter(u => u.role === 'operator').length}
          </span>
        </button>
      </div>

      {/* Cards de resumo */}
      <SummaryCards users={users} activeTab={activeTab} />

      {/* Tabela */}
      {activeTab === 'driver' ? (
        <DriversTable
          users={users}
          onEdit={handleEdit}
          onToggle={handleToggle}
          onDelete={handleDelete}
        />
      ) : (
        <OperatorsTable
          users={users}
          onEdit={handleEdit}
          onToggle={handleToggle}
          onDelete={handleDelete}
        />
      )}

      {/* Modal Criar/Editar */}
      {showModal && (
        <UserModal
          user={editingUser}
          defaultRole={activeTab}
          onClose={() => { setShowModal(false); setEditingUser(null) }}
          onSave={handleSave}
        />
      )}

      {/* Modal de Confirmação */}
      {confirmModal && (
        <ConfirmModal
          {...confirmModal}
          onClose={() => setConfirmModal(null)}
        />
      )}
    </AdminLayout>
  )
}