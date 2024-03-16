interface LayoutProps {
  content: React.ReactNode;
}

const Layout: React.FC<LayoutProps> = ({ content }) => {
  //indicating that the user is logged in

  return (
    <div className="flex flex-col h-screen bg-stadium bg-cover bg-middle-center text-gray-800">
      <main className="flex-grow flex items-center justify-center">
        {content}
      </main>
    </div>
  );
};

export default Layout;
