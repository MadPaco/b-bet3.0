import './index.css'
import Layout from './components/Layout';
import LoginPage from './pages/LoginPage';

function App() {
  return (
    <>
      <Layout content={<LoginPage />}/>
    </>
  )
}

export default App
