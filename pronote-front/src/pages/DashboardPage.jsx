import { useAuth } from '../context/AuthContext';
import EleveDashboard from './EleveDashboard';
import ProfesseurDashboard from './ProfesseurDashboard';

export default function DashboardPage() {
  const { user, logout } = useAuth();

    if (!user) return <p>Chargement...</p>;

    return (
        <>
            <nav className="navbar navbar-expand-lg bg-body-tertiary">
                <div className="container-fluid">
                    <a className="navbar-brand" href="#">Pronote</a>
                    <button className="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span className="navbar-toggler-icon"></span>
                    </button>
                    <div className="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul className="navbar-nav me-auto mb-2 mb-lg-0">
                            <li className="nav-item">
                                <a className="nav-link active" aria-current="page" href="#">Home</a>
                            </li>
                            <li className="nav-item">
                                <a className="nav-link" href="#">Link</a>
                            </li>
                        </ul>
                    </div>
                    <div className="nav-item">
                        <a className="nav-link" href="" onClick={logout}>Se déconnecter</a>
                    </div>
                </div>
             </nav>

            <div style={{ maxWidth: 1000, margin: '40px auto', fontFamily: 'sans-serif' }}>
                {user.role === 'ELEVE' && <EleveDashboard user={user} />}
                {user.role === 'PROFESSEUR' && <ProfesseurDashboard user={user} />}
                {user.role !== 'ELEVE' && user.role !== 'PROFESSEUR' && <p>Rôle inconnu.</p>}
            </div>
         </>
  );
}
