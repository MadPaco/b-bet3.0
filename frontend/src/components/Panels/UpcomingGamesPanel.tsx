import Panel from '../common/Panel';
import { useState, useEffect } from 'react';
import { fetchUpcomingGames } from '../../utility/api';

interface UpcomingGamesPanelProps { }

const UpcomingGamesPanel: React.FC<UpcomingGamesPanelProps> = () => {
    const [upcomingGames, setUpcomingGames] = useState<any[]>([]);
    const [loading, setLoading] = useState<boolean>(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const fetchGames = async () => {
            try {
                const response = await fetchUpcomingGames();
                const data = await response.json();
                setUpcomingGames(data);
            } catch (err) {
                setError('Failed to load upcoming games');
                console.error(err);
            } finally {
                setLoading(false);
            }
        };
        fetchGames();
    }, []);

    return (
        <Panel>
            <div className="flex items-center flex-col w-full">
                <h2 className='text-xl font-semibold mb-2'>Upcoming Games</h2>
                <div className="flex flex-wrap w-full -mx-3">
                    {loading ? (
                        'Loading...'
                    ) : error ? (
                        <div>{error}</div>
                    ) : upcomingGames.length > 0 ? (
                        upcomingGames.map((game: any, index: number) => (
                            <div
                                key={index}
                                className="w-full sm:w-1/3 px-3 mb-6"
                            >
                                <div className="text-center bg-gray-700 p-3 rounded-xl shadow-inner shadow-white flex justify-between items-center h-full">
                                    <div className="w-1/3 flex justify-center items-center">
                                        <img
                                            src={`/assets/images/teams/${game.awayTeamLogo}`}
                                            className="w-1/2 h-auto"
                                            alt={`${game.awayTeam} logo`}
                                        />
                                    </div>
                                    <div className="w-1/3 flex flex-col items-center">
                                        <h4>{game.date}</h4>
                                        <br />
                                        <h4 className="font-bold">
                                            {game.awayTeam} <br /> at <br /> {game.homeTeam}
                                        </h4>
                                        <br />
                                        <h4>{game.location}</h4>
                                    </div>
                                    <div className="w-1/3 flex justify-center items-center">
                                        <img
                                            src={`/assets/images/teams/${game.homeTeamLogo}`}
                                            className="w-1/2 h-auto"
                                            alt={`${game.homeTeam} logo`}
                                        />
                                    </div>
                                </div>
                            </div>
                        ))
                    ) : (
                        <div>No upcoming games found</div>
                    )}
                </div>
            </div>
        </Panel>
    );
};

export default UpcomingGamesPanel;
