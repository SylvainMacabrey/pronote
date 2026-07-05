import { createContext, useContext, useEffect, useState } from 'react';
import api from '../api/axios';

const AuthContext = createContext(null);

export function AuthProvider({ children }) {
  const [token, setToken] = useState(localStorage.getItem('token'));
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);

    const logout = () => {
        localStorage.removeItem('token');
        setToken(null);
        setUser(null);
    };

  const fetchProfil = async () => {
    try {
      const response = await api.get('/api/profil');
      setUser(response.data);
    } catch {
      logout();
    }
  };

  useEffect(() => {
    if (token) {
      fetchProfil().finally(() => setLoading(false));
    } else {
      setLoading(false);
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  const login = async (email, password) => {
    const response = await api.post('/api/login_check', { email, password });
    localStorage.setItem('token', response.data.token);
    setToken(response.data.token);
    await fetchProfil();
  };

  return (
    <AuthContext.Provider value={{ user, login, logout, isAuthenticated: !!token, loading }}>
      {children}
    </AuthContext.Provider>
  );
}

export function useAuth() {
  return useContext(AuthContext);
}
