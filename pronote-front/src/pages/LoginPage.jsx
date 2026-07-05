import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';

export default function LoginPage() {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState(null);
  const { login } = useAuth();
  const navigate = useNavigate();

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError(null);
    try {
      await login(email, password);
      navigate('/dashboard');
    } catch {
      setError('Email ou mot de passe incorrect.');
    }
  };

  return (
    <div style={{ maxWidth: 500, margin: '50px auto', fontFamily: 'sans-serif' }}>
      <h1>Connexion</h1>
      <form onSubmit={handleSubmit}>
        <div className="mb-3">
            <label htmlFor="exampleInputEmail1" className="form-label">Adresse mail</label>
            <input
                type="email"
                className="form-control"
                placeholder="Email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                required
            />
        </div>
        <div className="mb-3">
            <label htmlFor="exampleInputEmail1" className="form-label">Mot de passe</label>
            <input
                type="password"
                className="form-control"
                placeholder="Mot de passe"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                required
            />
        </div>
        {error && <div className="form-text" style={{ color: 'red' }}>{error}</div>}
        <button type="submit" className="btn btn-primary" style={{ width: '100%', padding: 10 }}>
          Se connecter
        </button>
      </form>
    </div>
  );
}
