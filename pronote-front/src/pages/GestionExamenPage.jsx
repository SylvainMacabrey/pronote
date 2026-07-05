import { useEffect, useMemo, useState } from 'react';
import { useParams, Link } from 'react-router-dom';
import api from '../api/axios';

export default function GestionExamenPage() {
  const { id } = useParams();

  const [eleves, setEleves] = useState([]);
  const [valeurs, setValeurs] = useState({});
  const [examenInfo, setExamenInfo] = useState(null);
  const [moyenneClasse, setMoyenneClasse] = useState(null);
  const [chargement, setChargement] = useState(true);
  const [erreur, setErreur] = useState(null);
  const [enregistrementEnCours, setEnregistrementEnCours] = useState(false);
  const [messageSucces, setMessageSucces] = useState(null);

  const chargerDonnees = async () => {
    setChargement(true);
    setErreur(null);
    try {
      const [reponseEleves, reponseNotes] = await Promise.all([
        api.get(`/api/examens/${id}/eleves`),
        api.get(`/api/examens/${id}/notes`),
      ]);


      setEleves(reponseEleves.data);
      setExamenInfo({
        intitule: reponseNotes.data.examen,
        classe: reponseNotes.data.classe,
      });
      setMoyenneClasse(reponseNotes.data.moyenne);

      const valeursInitiales = {};
      reponseNotes.data.notes.forEach((note) => {
        valeursInitiales[note.eleveId] = note.valeur;
      });
      setValeurs(valeursInitiales);
    } catch {
      setErreur("Impossible de charger les données de cet examen.");
    } finally {
      setChargement(false);
    }
  };

  useEffect(() => {
    chargerDonnees();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [id]);

  const moyenneLocale = useMemo(() => {
    const notesRemplies = Object.values(valeurs).filter((v) => v !== '' && v !== undefined && v !== null);
    if (notesRemplies.length === 0) return null;
    const somme = notesRemplies.reduce((acc, v) => acc + Number(v), 0);
    return Math.round((somme / notesRemplies.length) * 100) / 100;
  }, [valeurs]);

  const handleChange = (eleveId, value) => {
    setValeurs((prev) => ({ ...prev, [eleveId]: value }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setErreur(null);
    setMessageSucces(null);
    setEnregistrementEnCours(true);

    const notes = Object.entries(valeurs)
      .filter(([, valeur]) => valeur !== '' && valeur !== undefined && valeur !== null)
      .map(([eleveId, valeur]) => ({ eleveId: Number(eleveId), valeur: Number(valeur) }));

    try {
      await api.post(`/api/examens/${id}/notes`, { notes });
        setMessageSucces('Notes enregistrées avec succès.');
      await chargerDonnees();
    } catch (err) {
        setErreur(err.response?.data?.errors?.join?.(' / ') || "Erreur lors de l'enregistrement des notes.");
    } finally {
        setEnregistrementEnCours(false);
    }
  };

  if (chargement) return <div className="container py-4">Chargement...</div>;

  return (
    <div className="container py-4" style={{ maxWidth: 640 }}>
      <Link to="/dashboard" className="btn btn-link ps-0 mb-3">
        &larr; Retour au dashboard
      </Link>

      {examenInfo && (
        <>
          <h1 className="h4">{examenInfo.intitule}</h1>
          <p className="text-muted mb-4">Classe : {examenInfo.classe}</p>
        </>
      )}

      {erreur && <div className="alert alert-danger">{erreur}</div>}
      {messageSucces && <div className="alert alert-success">{messageSucces}</div>}

      <form onSubmit={handleSubmit}>
        <table className="table align-middle">
          <thead>
            <tr>
              <th>Élève</th>
              <th style={{ width: 140 }}>Note / 20</th>
            </tr>
          </thead>
          <tbody>
            {eleves.map((eleve) => (
              <tr key={eleve.id}>
                <td>{eleve.prenom} {eleve.nom}</td>
                <td>
                  <input
                    type="number"
                    className="form-control"
                    min="0"
                    max="20"
                    step="0.5"
                    value={valeurs[eleve.id] ?? ''}
                    onChange={(e) => handleChange(eleve.id, e.target.value)}
                  />
                </td>
              </tr>
            ))}
          </tbody>
        </table>

        <div className="d-flex justify-content-between align-items-center mt-4">
          <div>
            <p className="mb-0">
              <strong>Moyenne (saisie en cours) :</strong>{' '}
              {moyenneLocale !== null ? `${moyenneLocale} / 20` : '—'}
            </p>
            <p className="mb-0 text-muted">
              <strong>Moyenne enregistrée :</strong>{' '}
              {moyenneClasse !== null ? `${moyenneClasse} / 20` : '—'}
            </p>
          </div>

          <button type="submit" className="btn btn-primary" disabled={enregistrementEnCours}>
            {enregistrementEnCours ? 'Enregistrement...' : 'Enregistrer les notes'}
          </button>
        </div>
      </form>
    </div>
  );
}
