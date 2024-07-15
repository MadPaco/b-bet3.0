import { BrowserRouter as Router, Route, Routes } from 'react-router-dom';
import { lazy, Suspense } from 'react';
import { SyncLoader } from 'react-spinners';
import './index.css';
import LoginPage from './pages/LoginPage';
import IndexPage from './pages/IndexPage';
import RegisterPage from './pages/RegisterPage';
import DashboardPage from './pages/DashboardPage';
import { AuthProvider } from './components/auth/AuthProvider';
import { ColorProvider } from './context/ColorProvider';

// Lazy load less frequently used pages
const AdminPage = lazy(() => import('./pages/AdminPage'));
const SchedulePage = lazy(() => import('./pages/SchedulePage'));
const AllBetsPage = lazy(() => import('./pages/AllBetsPage'));
const AllUsersPage = lazy(() => import('./pages/AllUsersPage'));
const HeadToHeadPage = lazy(() => import('./pages/HeadToHeadPage'));
const LeaderboardPage = lazy(() => import('./pages/LeaderboardPage'));
const PredictionsPage = lazy(() => import('./pages/PredictionsPage'));
const ProfilePage = lazy(() => import('./pages/ProfilePage'));
const UserProfilePage = lazy(() => import('./pages/UserProfilePage'));
const RulesPage = lazy(() => import('./pages/RulesPage'));
const StatsPage = lazy(() => import('./pages/StatsPage'));
const AchievementsPage = lazy(() => import('./pages/AchievementsPage'));

const App: React.FC = () => {
  return (
    <Router>
      <AuthProvider>
        <ColorProvider>
          <Suspense
            fallback={
              <div className="flex justify-center items-center h-screen bg-gray-900">
                <SyncLoader color={'#36d7b7'} />
              </div>
            }
          >

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
              <Route path="/achievements" element={<AchievementsPage />} />
              <Route path="/leaderboard" element={<LeaderboardPage />} />
              <Route path="/predictions" element={<PredictionsPage />} />
              <Route path="/editProfile" element={<ProfilePage />} />
              <Route path="/users/:username" element={<UserProfilePage />} />
              <Route path="/rules" element={<RulesPage />} />
              <Route path="/stats" element={<StatsPage />} />
            </Routes>
          </Suspense>
        </ColorProvider>
      </AuthProvider>
    </Router>
  );
};

export default App;
