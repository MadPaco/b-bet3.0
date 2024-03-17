interface LayoutProps {
  content: React.ReactNode;
}

const Layout: React.FC<LayoutProps> = ({ content }) => {
  return (
    <div className="flex flex-row h-screen text-gray-800">
      <div className="fixed inset-0 h-full w-full bg-stadiumTop bg-cover bg-center z-0" />
      <div className="fixed inset-0 h-full w-full bg-black bg-opacity-50 z-10" />
      <main className="w-full flex-grow flex items-center justify-center z-20">
        {content}
      </main>
    </div>
  );
};

export default Layout;
