import Navigation from './Navigation';

const Header: React.FC = () => {
  return (
    <header className="bg-gray-900 flex justify-between items-center px-2">
      <h1 className="text-2xl font-bold text-orange-500">B-Bet</h1>
      <Navigation />
    </header>
  );
};

export default Header;
