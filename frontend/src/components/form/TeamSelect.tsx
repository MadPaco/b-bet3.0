import React from 'react';

interface TeamSelectProps {
    label: string;
    points: number;
    value: string | null;
    onChange: (e: React.ChangeEvent<HTMLSelectElement>) => void;
    options: string[];
}

const TeamSelect: React.FC<TeamSelectProps> = ({ label, points, value, onChange, options }) => {
    return (
        <div className="mb-4 text-highlightGold">
            <label>
                {label}
                <div className='text-sm text-white mb-2'>
                    {points} points
                </div>

                <select
                    className="mb-2 w-full text-white bg-gray-900 border-2 rounded-xl border-highlightCream text-center"
                    value={value}
                    onChange={onChange}
                >
                    <option value="">Select a team</option>
                    {options.map((team) => (
                        <option key={team} value={team}>
                            {team}
                        </option>
                    ))}
                </select>
            </label>
        </div>
    );
};

export default TeamSelect;
