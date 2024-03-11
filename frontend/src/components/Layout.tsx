import Header from './Header';
import Footer from './Footer';

interface LayoutProps {
  content: React.ReactNode;
}

const Layout: React.FC<LayoutProps> = ({ content }) => {
  return (
    <div className="flex flex-col min-h-screen bg-stadium bg-cover bg-middle-center text-gray-800">
      <Header />
      <main className="flex-grow flex items-center justify-center">
        {content}
      </main>
      <Footer />
    </div>
  );
};

export default Layout;
