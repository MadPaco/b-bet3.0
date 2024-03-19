import { BrowserRouter as Router, Route, Routes } from 'react-router-dom';
import './index.css';
import LoginPage from './pages/LoginPage';
import IndexPage from './pages/IndexPage';
import RegisterPage from './pages/RegisterPage';
import DashboardPage from './pages/DashboardPage';
import AdminPage from './pages/AdminPage';
import SchedulePage from './pages/SchedulePage';
import { AuthProvider } from './components/auth/AuthProvider';
import AllBetsPage from './pages/AllBetsPage';
import AllUsersPage from './pages/AllUsersPage';
import HeadToHeadPage from './pages/HeadToHeadPage';
import LeaderboardPage from './pages/LeaderboardPage';
import PredictionsPage from './pages/PredictionsPage';
import ProfilePage from './pages/ProfilePage';
import RulesPage from './pages/RulesPage';
import StatsPage from './pages/StatsPage';
import { ColorProvider } from './context/ColorProvider';

const App: React.FC = () => {
  return (
    <Router>
      <AuthProvider>
        <ColorProvider>
          <Routes>
            <Route path="/login" element={<LoginPage />} />
            <Route path="/" element={<IndexPage />} />
            <Route path="/register" element={<RegisterPage />} />
            <Route path="/dashboard" element={<DashboardPage />} />
            <Route path="/admin" element={<AdminPage />} />
            <Route path="/schedule" element={<SchedulePage />} />
            <Route path="/allBets" element={<AllBetsPage />} />
            <Route path="/allUsers" element={<AllUsersPage />} />
            <Route path="/1vs1" element={<HeadToHeadPage />} />
            <Route path="/leaderboard" element={<LeaderboardPage />} />
            <Route path="/predictions" element={<PredictionsPage />} />
            <Route path="/myProfile" element={<ProfilePage />} />
            <Route path="/rules" element={<RulesPage />} />
            <Route path="/stats" element={<StatsPage />} />
          </Routes>
        </ColorProvider>
      </AuthProvider>
    </Router>
  );
};

export default App;
