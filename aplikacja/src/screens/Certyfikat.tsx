import { konto } from '../konto'
import { useStore } from '../store'
import { Lilek } from '../components/Lilek'

export function Certyfikat({ onClose }: { onClose: () => void }) {
  const s = useStore()
  const imie = s.name || konto?.imie || ''
  const data = new Date().toLocaleDateString('pl-PL', { day: 'numeric', month: 'long', year: 'numeric' })
  return (
    <div className="cert-wrap">
      <div className="cert">
        <Lilek mood="cheer" size={90} />
        <p className="cert-nad">Certyfikat ukończenia</p>
        <h1>Akademia Guliwera</h1>
        <p>Potwierdzamy, że</p>
        <h2>{imie || '________________'}</h2>
        <p>ukończył(a) wszystkie lekcje Akademii Guliwera: język finansów, metryki biznesu, ekonomia, sprawozdania finansowe, myślenie konsultingowe, strategia, prowadzenie zespołu oraz makroekonomia i inwestycje.</p>
        <p className="cert-data">{data} · Angelika Pruchniak-Gąszczyk, Guliwera 4 · guliwera4.pl</p>
      </div>
      <div className="row cert-akcje">
        <button className="btn" onClick={() => window.print()}>Drukuj / zapisz PDF</button>
        <button className="btn ghost" onClick={onClose}>Wróć</button>
      </div>
    </div>
  )
}
