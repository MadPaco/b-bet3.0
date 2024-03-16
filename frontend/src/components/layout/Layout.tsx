interface LayoutProps {
  content: React.ReactNode;
}

const Layout: React.FC<LayoutProps> = ({ content }) => {
  return (
    <div className="flex flex-row h-screen bg-grassfield bg-cover bg-top-right text-gray-800">
      <main className="flex-grow flex items-center justify-center">
        {content}
      </main>
    </div>
  );
};

export default Layout;
