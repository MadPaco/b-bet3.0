import Panel from '../components/common/Panel';
import LoggedInLayout from '../components/layout/LoggedInLayout';
const RulesPage: React.FC = () => {
  return (
    <LoggedInLayout>
      <div className="flex flex-col lg:pt-10 lg:grid lg:grid-cols-3 lg:grid-rows-3">
        <div className="lg:col-span-6 lg:row-span-3 text-white">
          <Panel
            children={
              <>
                <h1>Rules </h1>
                <p>
                  <ul>
                    <li>
                      A correct bet (meaning that home and away score were
                      correct) gives 5 points.
                    </li>
                    <li>
                      The correct margin (meaning picking the right winner, the
                      difference is correct but the results were not) gives 3
                      points.
                    </li>
                    <li>Picking the right winner gives 1 point.</li>
                  </ul>
                  <p>
                    FAQ:
                    Q:What is a hit?
                    A:A hit is when you score atleast 1 point, meaning you correctly predicted the winner.
                  </p>
                </p>
              </>
            }
          />
        </div>
      </div>
    </LoggedInLayout>
  );
};
export default RulesPage;
