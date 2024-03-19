import Panel from '../common/Panel';
interface NewsPanelProps {}

const NewsPanel: React.FC<NewsPanelProps> = () => {
  return (
    <Panel>
      <div className="flex items-center h-full text-gray-200">
        <div className="flex items-center">
          <div>
            <p>Changelog:</p>
            <ul>
              <li>pointless change that fucks up everything</li>
              <li>change that unfucks everything</li>
            </ul>
          </div>
        </div>
      </div>
    </Panel>
  );
};

export default NewsPanel;
