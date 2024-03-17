import React from 'react';
import Panel from '../../common/Panel';
import { useAuth } from '../../auth/AuthContext';

interface NewsPanelProps {
  color: string;
}

const NewsPanel: React.FC<NewsPanelProps> = ({ color }) => {
  const { username } = useAuth();

  return (
    <Panel color={color}>
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
