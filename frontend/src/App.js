import React, { createContext, useContext, useState, useEffect } from 'react';
// import './styles.css'; // Import the CSS file

// API Service Class
class ApiService {
  constructor() {
    this.baseURL = 'http://localhost:8000/api'; // Change to your Laravel URL
    this.token = localStorage.getItem('token');
  }

  async request(endpoint, options = {}) {
    const url = `${this.baseURL}${endpoint}`;
    const config = {
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        ...options.headers,
      },
      ...options,
    };

    if (this.token) {
      config.headers.Authorization = `Bearer ${this.token}`;
    }

    try {
      const response = await fetch(url, config);
      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.message || `HTTP error! status: ${response.status}`);
      }

      return data;
    } catch (error) {
      console.error('API Request Error:', error);
      throw error;
    }
  }

  // Auth methods
  async login(email, password) {
    const data = await this.request('/login', {
      method: 'POST',
      body: JSON.stringify({ email, password }),
    });
    debugger
    if (data.data.token) {
      this.setToken(data.data.token);
    }
    return data.data;
  }

  async logout() {
    try {
      await this.request('/logout', { method: 'POST' });
    } finally {
      this.clearToken();
    }
  }

  async getUser() {
    return this.request('/user');
  }

  setToken(token) {
    console.log('Setting token:', token.substring(0, 20) + '...');
    this.token = token;
    localStorage.setItem('token', token);
  }

  clearToken() {
    this.token = null;
    localStorage.removeItem('token');
  }

  // Employee methods
  async getEmployees() {
    return this.request('/employees');
  }

  async createEmployee(employeeData) {
    return this.request('/employees', {
      method: 'POST',
      body: JSON.stringify(employeeData),
    });
  }

  async updateEmployee(id, employeeData) {
    return this.request(`/employees/${id}`, {
      method: 'PUT',
      body: JSON.stringify(employeeData),
    });
  }

  async deleteEmployee(id) {
    return this.request(`/employees/${id}`, {
      method: 'DELETE',
    });
  }
}

const apiService = new ApiService();

// Auth Context
const AuthContext = createContext();

// Auth Provider Component
function AuthProvider({ children }) {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    const token = localStorage.getItem('token');
    if (token) {
      // Make sure apiService has the token
      apiService.token = token;
      
      apiService.getUser()
        .then(userData => setUser(userData))
        .catch(() => {
          apiService.clearToken();
          setError('Session expired');
        })
        .finally(() => setLoading(false));
    } else {
      setLoading(false);
    }
  }, []);

  const login = async (email, password) => {
    try {
      setError(null);
      const data = await apiService.login(email, password);
      setUser(data.user);
      return data;
    } catch (err) {
      setError(err.message);
      throw err;
    }
  };

  const logout = async () => {
    try {
      await apiService.logout();
    } finally {
      setUser(null);
      setError(null);
    }
  };

  return (
    <AuthContext.Provider value={{ user, login, logout, loading, error, setError }}>
      {children}
    </AuthContext.Provider>
  );
}

// Custom hook to use auth context
const useAuth = () => {
  const context = useContext(AuthContext);
  if (!context) {
    throw new Error('useAuth must be used within AuthProvider');
  }
  return context;
};

// Employee Form Component
function EmployeeForm({ employee, onClose }) {
  const [formData, setFormData] = useState({
    first_name: employee?.first_name || '',
    last_name: employee?.last_name || '',
    email: employee?.email || '',
    phone: employee?.phone || '',
    address: employee?.address || '',
    salary: employee?.salary || '',
    department_id: employee?.department_id || '',
    role_id: employee?.role_id || '',
    status: employee?.status || 'active',
    hire_date: employee?.hire_date ? employee.hire_date.split('T')[0] : '',
  });
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);

  const handleChange = (e) => {
    setFormData({
      ...formData,
      [e.target.name]: e.target.value
    });
  };

  const handleSubmit = async () => {
    setLoading(true);
    setError(null);

    try {
      if (employee) {
        await apiService.updateEmployee(employee.id, formData);
      } else {
        await apiService.createEmployee(formData);
      }
      onClose();
    } catch (err) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="modal-overlay">
      <div className="modal-content">
        <div className="modal-header">
          <h3 className="modal-title">
            {employee ? 'Edit Employee' : 'Add Employee'}
          </h3>
        </div>
        
        <div className="modal-body">
          {error && (
            <div className="alert alert-error">
              {error}
            </div>
          )}
          
          <div className="form-section">
            <div className="form-group">
              <label className="form-label">
                First Name
              </label>
              <input
                type="text"
                name="first_name"
                value={formData.first_name}
                onChange={handleChange}
                required
                className="form-input"
              />
            </div>

            <div className="form-group">
              <label className="form-label">
                Last Name
              </label>
              <input
                type="text"
                name="last_name"
                value={formData.last_name}
                onChange={handleChange}
                required
                className="form-input"
              />
            </div>
            
            <div className="form-group">
              <label className="form-label">
                Email
              </label>
              <input
                type="email"
                name="email"
                value={formData.email}
                onChange={handleChange}
                required
                className="form-input"
              />
            </div>

            <div className="form-group">
              <label className="form-label">
                Phone
              </label>
              <input
                type="tel"
                name="phone"
                value={formData.phone}
                onChange={handleChange}
                className="form-input"
              />
            </div>

            <div className="form-group">
              <label className="form-label">
                Address
              </label>
              <textarea
                name="address"
                value={formData.address}
                onChange={handleChange}
                rows="2"
                className="form-input form-textarea"
              />
            </div>

            <div className="form-group">
              <label className="form-label">
                Department ID
              </label>
              <input
                type="number"
                name="department_id"
                value={formData.department_id}
                onChange={handleChange}
                required
                className="form-input"
              />
            </div>

            <div className="form-group">
              <label className="form-label">
                Role ID
              </label>
              <input
                type="number"
                name="role_id"
                value={formData.role_id}
                onChange={handleChange}
                required
                className="form-input"
              />
            </div>

            <div className="form-group">
              <label className="form-label">
                Hire Date
              </label>
              <input
                type="date"
                name="hire_date"
                value={formData.hire_date}
                onChange={handleChange}
                className="form-input"
              />
            </div>
            
            <div className="form-group">
              <label className="form-label">
                Salary
              </label>
              <input
                type="number"
                name="salary"
                value={formData.salary}
                onChange={handleChange}
                step="0.01"
                className="form-input"
              />
            </div>

            <div className="form-group">
              <label className="form-label">
                Status
              </label>
              <div className="radio-group">
                <div className="radio-item">
                  <input
                    type="radio"
                    name="status"
                    value="active"
                    checked={formData.status === 'active'}
                    onChange={handleChange}
                    className="radio-input"
                  />
                  <span className="radio-label">Active</span>
                </div>
                <div className="radio-item">
                  <input
                    type="radio"
                    name="status"
                    value="inactive"
                    checked={formData.status === 'inactive'}
                    onChange={handleChange}
                    className="radio-input"
                  />
                  <span className="radio-label">Inactive</span>
                </div>
              </div>
            </div>
            
            <div className="form-actions">
              <button
                type="button"
                onClick={onClose}
                className="btn btn-secondary"
              >
                Cancel
              </button>
              <button
                type="button"
                onClick={handleSubmit}
                disabled={loading}
                className="btn btn-primary"
              >
                {loading ? 'Saving...' : (employee ? 'Update' : 'Create')}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

// Employee List Component
function EmployeeList() {
  const [employees, setEmployees] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [showForm, setShowForm] = useState(false);
  const [editingEmployee, setEditingEmployee] = useState(null);

  const loadEmployees = async () => {
    try {
      setError(null);
      const data = await apiService.getEmployees();
      let employeesArray = data?.data?.employees;
      setEmployees(employeesArray);
    } catch (err) {
      setError('Failed to load employees: ' + err.message);
      setEmployees([]);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    loadEmployees();
  }, []);

  const handleDelete = async (id) => {
    if (!window.confirm('Are you sure you want to delete this employee?')) return;
    
    try {
      await apiService.deleteEmployee(id);
      setEmployees(employees.filter(emp => emp.id !== id));
    } catch (err) {
      setError('Failed to delete employee: ' + err.message);
    }
  };

  const handleEdit = (employee) => {
    setEditingEmployee(employee);
    setShowForm(true);
  };

  const handleFormClose = () => {
    setShowForm(false);
    setEditingEmployee(null);
    loadEmployees();
  };

  if (loading) {
    return (
      <div className="loading-container">
        <div className="loading-text">Loading employees...</div>
      </div>
    );
  }

  return (
    <div>
      <div className="employee-list-header">
        <h1 className="employee-list-title">Employee Management</h1>
        <button
          onClick={() => setShowForm(true)}
          className="btn btn-primary btn-large"
        >
          Add Employee
        </button>
      </div>

      {error && (
        <div className="alert alert-error">
          {error}
        </div>
      )}

      {showForm && (
        <EmployeeForm
          employee={editingEmployee}
          onClose={handleFormClose}
        />
      )}

      <div className="employee-list-container">
        <ul className="employee-list">
          {employees.length === 0 ? (
            <li className="employee-empty">
              No employees found. Add some employees to get started!
            </li>
          ) : (
            employees.map((employee) => (
              <li key={employee.id} className="employee-item">
                <div className="employee-item-content">
                  <div className="employee-info">
                    <p className="employee-name">
                      {employee.first_name} {employee.last_name}
                    </p>
                    <p className="employee-details">
                      {employee.email} • {employee.department?.name || 'N/A'} • {employee.role?.name || 'N/A'}
                    </p>
                    <p className="employee-status">
                      Status: <span style={{fontWeight: '600'}}>{employee.status}</span>
                    </p>
                  </div>
                  <div className="employee-actions">
                    <button
                      onClick={() => handleEdit(employee)}
                      className="btn btn-secondary"
                    >
                      Edit
                    </button>
                    <button
                      onClick={() => handleDelete(employee.id)}
                      className="btn btn-primary"
                    >
                      Delete
                    </button>
                  </div>
                </div>
              </li>
            ))
          )}
        </ul>
      </div>
    </div>
  );
}

// Login Component
function Login() {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const { login, error, setError } = useAuth();

  const handleSubmit = async () => {
    if (!email || !password) {
      setError('Please fill in all fields');
      return;
    }

    setIsLoading(true);
    try {
      await login(email, password);
    } catch (err) {
      // Error is handled in context
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className="login-container">
      <div className="login-form">
        <div>
          <h2 className="login-title">
            Employee Management System
          </h2>
          <p className="login-subtitle">
            Sign in to your account
          </p>
        </div>
        <div className="login-form-section">
          {error && (
            <div className="alert alert-error">
              {error}
            </div>
          )}
          <div className="form-group">
            <input
              id="email"
              type="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              placeholder="Email address"
              className="form-input"
              disabled={isLoading}
            />
          </div>
          <div className="form-group">
            <input
              id="password"
              type="password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              placeholder="Password"
              className="form-input"
              disabled={isLoading}
            />
          </div>
          <div>
            <button
              type="button"
              onClick={handleSubmit}
              disabled={isLoading}
              className="btn btn-primary btn-full btn-large"
            >
              {isLoading ? 'Signing in...' : 'Sign in'}
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}

// Dashboard Component
function Dashboard() {
  const { user, logout } = useAuth();

  return (
    <div className="app-container">
      <nav className="navbar">
        <div className="navbar-container">
          <div className="navbar-content">
            <div>
              <h1 className="navbar-title">
                Employee Management System
              </h1>
            </div>
            <div className="navbar-user-section">
              <span className="navbar-welcome">
                Welcome, {user.data?.user?.name}
              </span>
              <button
                onClick={logout}
                className="btn btn-primary"
              >
                Logout
              </button>
            </div>
          </div>
        </div>
      </nav>
      
      <main className="main-content">
        <EmployeeList />
      </main>
    </div>
  );
}

// Main App Component
export default function App() {
  return (
    <AuthProvider>
      <AppContent />
    </AuthProvider>
  );
}

// App Content Component (inside AuthProvider)
function AppContent() {
  const { user, loading } = useAuth();

  if (loading) {
    return (
      <div className="loading-container">
        <div className="loading-text">Loading...</div>
      </div>
    );
  }

  return user ? <Dashboard /> : <Login />;
}