import { useEffect, useState } from 'react';
import api from '../api/axios';
import RadarNotes from '../components/RadarNotes';

export default function EleveDashboard({ user }) {
  const [matieres, setMatieres] = useState([]);
  const [moyenneGenerale, setMoyenneGenerale] = useState(null);
  const [chargement, setChargement] = useState(true);
  const [erreur, setErreur] = useState(null);

  useEffect(() => {
    api
      .get('/api/mes-notes')
      .then(({ data }) => {
        setMatieres(data.matieres);
        setMoyenneGenerale(data.moyenneGenerale);
      })
      .catch(() => setErreur('Impossible de charger vos notes.'))
      .finally(() => setChargement(false));
  }, []);

    if (chargement) return <div className="container py-4">Chargement...</div>;

    const moyennesClasseParMatiere = {};
    matieres.forEach((matiere) => {
        const valeurs = matiere.notes
            .map((note) => note.moyenneClasse)
            .filter((v) => v !== null && v !== undefined);

        moyennesClasseParMatiere[matiere.matiere] =
        valeurs.length > 0 ? Math.round((valeurs.reduce((a, b) => a + b, 0) / valeurs.length) * 100) / 100 : null;
    });

  return (
    <div className="container py-4">
      <div className="d-flex justify-content-between align-items-center mb-4">
        <h1 className="h3 mb-0">Bonjour, {user.prenom} {user.nom}</h1>
      </div>

      {erreur && <div className="alert alert-danger">{erreur}</div>}

        {!erreur && (
            <div className="alert alert-primary d-flex justify-content-between align-items-center">
            <span className="fw-bold">Moyenne générale</span>
            <span className="fs-4">{moyenneGenerale !== null ? `${moyenneGenerale} / 20` : '—'}</span>
            </div>
        )}

        {!erreur && matieres.length > 0 && (
            <div className="card mb-4">
                <div className="card-body">
                    <h2 className="h6 mb-3">Vue d'ensemble par matière</h2>
                    <div style={{ maxWidth: 500, margin: '0 auto' }}>
                    <RadarNotes matieres={matieres} moyennesClasseParMatiere={moyennesClasseParMatiere} />
                    </div>
                </div>
            </div>
        )}

      {matieres.length === 0 && !erreur && (
        <p className="text-muted">Aucune note enregistrée pour le moment.</p>
      )}

      {matieres.map((matiere) => (
        <div className="card mb-4" key={matiere.matiere}>
          <div className="card-header d-flex justify-content-between align-items-center">
            <span><strong>{matiere.matiere}</strong></span>
            <span>Moyenne : <strong>{matiere.moyenne} / 20</strong></span>
          </div>
          <div className="card-body p-0">
            <table className="table table-striped mb-0">
              <thead>
                <tr>
                  <th>Examen</th>
                  <th>Date</th>
                  <th>Note / 20</th>
                  <th>Moyenne de classe</th>
                </tr>
              </thead>
              <tbody>
                {matiere.notes.map((note, index) => (
                  <tr key={index}>
                    <td>{note.examen}</td>
                    <td>{note.date}</td>
                    <td>{note.valeur}</td>
                    <td className="text-muted">
                      {note.moyenneClasse !== null ? `${note.moyenneClasse} / 20` : '—'}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      ))}
    </div>
  );
}
