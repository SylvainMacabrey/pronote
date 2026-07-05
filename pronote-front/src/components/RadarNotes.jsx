// src/components/RadarNotes.jsx
import { Radar } from 'react-chartjs-2';
import {
  Chart as ChartJS,
  RadialLinearScale,
  PointElement,
  LineElement,
  Filler,
  Tooltip,
  Legend,
} from 'chart.js';

ChartJS.register(RadialLinearScale, PointElement, LineElement, Filler, Tooltip, Legend);

export default function RadarNotes({ matieres, moyennesClasseParMatiere }) {
  const data = {
    labels: matieres.map((m) => m.matiere),
    datasets: [
      {
        label: 'Mes moyennes',
        data: matieres.map((m) => m.moyenne),
        backgroundColor: 'rgba(13, 110, 253, 0.2)',
        borderColor: 'rgba(13, 110, 253, 1)',
        pointBackgroundColor: 'rgba(13, 110, 253, 1)',
      },
      {
        label: 'Moyenne de la classe',
        data: matieres.map((m) => moyennesClasseParMatiere[m.matiere] ?? null),
        backgroundColor: 'rgba(108, 117, 125, 0.15)',
        borderColor: 'rgba(108, 117, 125, 1)',
        pointBackgroundColor: 'rgba(108, 117, 125, 1)',
      },
    ],
  };

  const options = {
    scales: {
      r: {
        min: 0,
        max: 20,
        ticks: { stepSize: 5 },
      },
    },
  };

  return <Radar data={data} options={options} />;
}
