import { useState } from 'react';
import { useNavigate, useSearchParams, Link } from 'react-router-dom';
import api from '../api/axios';

export default function CreerExamenPage() {
  const [searchParams] = useSearchParams();
  const affectationId = searchParams.get('affectationId');
  const navigate = useNavigate();

  const [intitule, setIntitule] = useState('');
  const [dateExamen, setDateExamen] = useState('');
  const [erreur, setErreur] = useState(null);
  const [envoiEnCours, setEnvoiEnCours] = useState(false);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setErreur(null);
    setEnvoiEnCours(true);

    try {
      const response = await api.post('/api/examens', {
        affectationId: Number(affectationId),
        intitule,
        dateExamen,
      });

      navigate(`/professeur/examens/${response.data.id}/notes`, {
        state: { affectationId: Number(affectationId), examen: response.data },
      });
    } catch (err) {
      setErreur(
        err.response?.data?.error ||
          err.response?.data?.errors ||
          "Impossible de créer l'examen."
      );
    } finally {
      setEnvoiEnCours(false);
    }
  };

  if (!affectationId) {
    return (
      <div className="container py-4">
        <div className="alert alert-warning">
          Aucune affectation sélectionnée.{' '}
          <Link to="/dashboard">Retour au dashboard</Link>
        </div>
      </div>
    );
  }

  return (
    <div className="container py-4" style={{ maxWidth: 480 }}>
      <Link to="/dashboard" className="btn btn-link ps-0 mb-3">
        &larr; Retour
      </Link>

      <h1 className="h4 mb-4">Créer un examen</h1>

      <form onSubmit={handleSubmit}>
        <div className="mb-3">
          <label className="form-label">Intitulé</label>
          <input
            type="text"
            className="form-control"
            value={intitule}
            onChange={(e) => setIntitule(e.target.value)}
            placeholder="Contrôle chapitre 3"
            required
          />
        </div>

        <div className="mb-3">
          <label className="form-label">Date de l'examen</label>
          <input
            type="date"
            className="form-control"
            value={dateExamen}
            onChange={(e) => setDateExamen(e.target.value)}
            required
          />
        </div>

        {erreur && <div className="alert alert-danger">{String(erreur)}</div>}

        <button type="submit" className="btn btn-primary w-100" disabled={envoiEnCours}>
          {envoiEnCours ? 'Création...' : "Créer l'examen"}
        </button>
      </form>
    </div>
  );
}
