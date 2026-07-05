import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import api from '../api/axios';
import { useAuth } from '../context/AuthContext';

export default function ProfesseurDashboard() {
  const { user, logout } = useAuth();
  const [nomsClasses, setNomsClasses] = useState([]);
  const [classeActive, setClasseActive] = useState(null);
  const [donneesParClasse, setDonneesParClasse] = useState({});
  const [chargementClasse, setChargementClasse] = useState(false);
  const [erreur, setErreur] = useState(null);
  const [chargementInitial, setChargementInitial] = useState(true);

    // Une seule requête par classe, calculée entièrement côté backend
  const chargerClasse = async (nomClasse) => {
    if (donneesParClasse[nomClasse]) return; // déjà en cache

    setChargementClasse(true);
    setErreur(null);

    try {
        const { data } = await api.get(`/api/mes-classes/${nomClasse}/donnees`);
        console.log("data: ", data)
        setDonneesParClasse((prev) => ({ ...prev, [nomClasse]: data }));
    } catch {
        setErreur(`Impossible de charger la classe ${nomClasse}.`);
    } finally {
        setChargementClasse(false);
    }
  };

  // Une seule requête légère : juste les noms de classes attribuées
  useEffect(() => {
    api
      .get('/api/mes-classes')
      .then(({ data }) => {
        setNomsClasses(data);
          if (data.length > 0) {
          setClasseActive(data[0]);
          chargerClasse(data[0]);
        }
      })
      .catch(() => setErreur('Impossible de charger vos classes.'))
      .finally(() => setChargementInitial(false));
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  const handleClicOnglet = (nomClasse) => {
    setClasseActive(nomClasse);
    chargerClasse(nomClasse);
  };

  if (chargementInitial) return <div className="container py-4">Chargement...</div>;

  const affectationsAffichees = donneesParClasse[classeActive] || [];

  return (
    <div className="container py-4">
      <div className="d-flex justify-content-between align-items-center mb-4">
        <h1 className="h3 mb-0">Bonjour, {user.prenom} {user.nom}</h1>
      </div>

      {erreur && <div className="alert alert-danger">{erreur}</div>}

      {nomsClasses.length === 0 && (
        <p className="text-muted">Aucune classe ne vous est attribuée pour le moment.</p>
      )}

      {nomsClasses.length > 0 && (
        <>
          <ul className="nav nav-pills mb-4 flex-wrap">
            {nomsClasses.map((nom) => (
              <li className="nav-item" key={nom}>
                <button
                  type="button"
                  className={`nav-link ${nom === classeActive ? 'active' : ''}`}
                  onClick={() => handleClicOnglet(nom)}
                >
                  {nom}
                </button>
              </li>
            ))}
          </ul>

          {chargementClasse && !donneesParClasse[classeActive] && (
            <p className="text-muted">Chargement de la classe {classeActive}...</p>
          )}

          {affectationsAffichees.map((classe) => (
            <div className="card mb-4" key={classe.id}>
              <div className="card-header d-flex justify-content-between align-items-center">
                <span><strong>{classe.classe}</strong> — {classe.matiere}</span>
                <Link
                  to={`/professeur/examens/nouveau?affectationId=${classe.id}`}
                  className="btn btn-sm btn-primary"
                >
                  + Nouvel examen
                </Link>
              </div>

              <div className="card-body">
                {classe.examens.length === 0 && (
                  <p className="text-muted mb-0">Aucun examen créé pour cette classe/matière.</p>
                )}

                {classe.examens.map((examen) => (
                  <div className="mb-4" key={examen.id}>
                    <div className="d-flex justify-content-between align-items-center mb-2">
                      <h3 className="h6 mb-0">
                        {examen.intitule} <span className="text-muted">({examen.dateExamen})</span>
                      </h3>
                      <div>
                        <Link
                          to={`/professeur/examens/${examen.id}/notes`}
                          className="btn btn-sm btn-primary"
                        >
                          Saisir / modifier les notes
                        </Link>
                      </div>
                    </div>

                    {examen.notes.length === 0 ? (
                      <p className="text-muted">Aucune note saisie pour cet examen.</p>
                    ) : (
                      <table className="table table-sm table-striped mb-1">
                        <thead>
                          <tr>
                            <th>Élève</th>
                            <th>Note / 20</th>
                          </tr>
                        </thead>
                        <tbody>
                          {examen.notes.map((note) => (
                            <tr key={note.eleveId}>
                              <td>{note.eleve}</td>
                              <td>{note.valeur}</td>
                            </tr>
                          ))}
                        </tbody>
                      </table>
                    )}

                    <p className="mb-0">
                      <strong>Moyenne de classe :</strong>{' '}
                      {examen.moyenneClasse !== null ? `${examen.moyenneClasse} / 20` : '—'}
                    </p>
                  </div>
                ))}
              </div>
            </div>
          ))}
        </>
      )}
    </div>
  );
}
