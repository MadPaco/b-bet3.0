
import { fetchHiddenCompletion, fetchNonHiddenCompletion } from '../../utility/api';
import { useState, useEffect } from 'react';
import { useParams } from 'react-router-dom';

interface completion {
    earned: number;
    total: number;
}



const AchievementEarnedOverview: React.FC = () => {
    const { username } = useParams<{ username: string }>();
    const [hiddenCompletion, setHiddenCompletion] = useState<completion | null>(null);
    const [nonHiddenCompletion, setNonHiddenCompletion] = useState<completion | null>(null);

    useEffect(() => {
        const loadHiddenCompletion = async () => {
            try {
                const data = await fetchHiddenCompletion(username);
                setHiddenCompletion(data);
            } catch (error) {
                console.error('Error fetching hidden completion:', error);
            }
        };

        loadHiddenCompletion();
    }, [username]);

    useEffect(() => {
        const loadNonHiddenCompletion = async () => {
            try {
                const data = await fetchNonHiddenCompletion(username);
                setNonHiddenCompletion(data);
            } catch (error) {
                console.error('Error fetching non-hidden completion:', error);
            }
        };

        loadNonHiddenCompletion();
    }, [username]);

    return (
        <>
            <div className="text-center flex flex-col items-center mb-3 text-highlightCream">
                <h1 className='font-bold'>Earned total achievements</h1>
                <div className="relative w-full lg:w-1/2 bg-gray-300 rounded-full h-6 my-2 text-black">
                    {nonHiddenCompletion && hiddenCompletion && nonHiddenCompletion.earned > 0 && (
                        <div>{nonHiddenCompletion.earned + hiddenCompletion.earned} / {nonHiddenCompletion.total + hiddenCompletion.total}</div>
                    )}
                    {nonHiddenCompletion && hiddenCompletion && nonHiddenCompletion.earned > 0 && (
                        <div
                            className={`absolute top-0 left-0 h-6 rounded-full bg-green-600 text-white flex items-center justify-center`}
                            style={{ width: `${(nonHiddenCompletion.earned / (nonHiddenCompletion.total + hiddenCompletion.total)) * 100}%` }}
                        >

                        </div>
                    )}
                </div>
                <h1 className='font-bold' >Earned <span className='text-highlightGold'><i>hidden</i></span> achievements: </h1>
                <div className="relative w-full lg:w-1/2 bg-gray-300 rounded-full h-6 mt-2 text-black">
                    {hiddenCompletion && hiddenCompletion.earned > 0 && (<div>{hiddenCompletion.earned} / {hiddenCompletion.total}</div>)}

                    {hiddenCompletion && hiddenCompletion.earned > 0 && (
                        <div
                            className={`absolute top-0 left-0 h-6 rounded-full bg-green-600 text-white flex items-center justify-center`}
                            style={{ width: `${(hiddenCompletion.earned / hiddenCompletion.total) * 100}%` }}
                        >
                        </div>
                    )}

                </div>
            </div>
        </>

    )
};

export default AchievementEarnedOverview;