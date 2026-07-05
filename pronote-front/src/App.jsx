import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import { AuthProvider } from './context/AuthContext';
import ProtectedRoute from './components/ProtectedRoute';
import LoginPage from './pages/LoginPage';
import DashboardPage from './pages/DashboardPage';
import CreerExamenPage from './pages/CreerExamenPage';
import GestionExamenPage from './pages/GestionExamenPage';

export default function App() {
  return (
    <BrowserRouter>
        <AuthProvider>
            <Routes>
                <Route path="/login" element={<LoginPage />} />

                <Route
                    path="/dashboard"
                    element={
                        <ProtectedRoute>
                            <DashboardPage />
                        </ProtectedRoute>
                    }
                />

                <Route
                    path="/professeur/examens/nouveau"
                    element={
                    <ProtectedRoute role="PROFESSEUR">
                        <CreerExamenPage />
                    </ProtectedRoute>
                    }
                />

                <Route
                    path="/professeur/examens/:id/notes"
                    element={
                    <ProtectedRoute role="PROFESSEUR">
                        <GestionExamenPage />
                    </ProtectedRoute>
                    }
                />

                <Route path="*" element={<Navigate to="/dashboard" replace />} />
            </Routes>
        </AuthProvider>
    </BrowserRouter>
  );
}
