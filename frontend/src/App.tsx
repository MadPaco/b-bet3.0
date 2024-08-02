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
const PreseasonPredictionPage = lazy(() => import('./pages/PreseasonPredictionPage'));
const LeaderboardPage = lazy(() => import('./pages/LeaderboardPage'));
const PredictionsPage = lazy(() => import('./pages/PredictionsPage'));
const EditProfilePage = lazy(() => import('./pages/EditProfilePage'));
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
              <Route path="/users/all" element={<AllUsersPage />} />
              <Route path="/preseasonPredictions" element={<PreseasonPredictionPage />} />
              <Route path="/users/:username/achievements" element={<AchievementsPage />} />
              <Route path="/leaderboard" element={<LeaderboardPage />} />
              <Route path="/predictions" element={<PredictionsPage />} />
              <Route path="/users/:username/profile/edit" element={<EditProfilePage />} />
              <Route path="/users/:username/profile" element={<UserProfilePage />} />
              <Route path="/rules" element={<RulesPage />} />
              <Route path="/users/:username/stats" element={<StatsPage />} />
            </Routes>
          </Suspense>
        </ColorProvider>
      </AuthProvider>
    </Router>
  );
};

export default App;
