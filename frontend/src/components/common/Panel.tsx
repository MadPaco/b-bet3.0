interface PanelProps {
  children: React.ReactNode;
}

const Panel: React.FC<PanelProps> = ({ children }) => (
  <div className="flex flex-col p-5 m-6 hover:bg-teal-300 bg-teal-400 bg-opacity-40 cursor-pointer rounded-md backdrop-blur-sm">
    <div className="flex items-center mb-4"></div>
    {children}
  </div>
);

export default Panel;
