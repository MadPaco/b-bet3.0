import Header from './Header';
import Footer from './Footer';
import { useAuth } from './AuthContext';

interface LayoutProps {
  content: React.ReactNode;
}

const Layout: React.FC<LayoutProps> = ({ content }) => {
  //indicating that the user is logged in
  const { username } = useAuth();

  return (
    <div className="flex flex-col min-h-screen bg-stadium bg-cover bg-middle-center text-gray-800">
      {username && <Header />}
      <main className="flex-grow flex items-center justify-center">
        {content}
      </main>
      <Footer />
    </div>
  );
};

export default Layout;
