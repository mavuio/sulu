// @flow
import React from 'react';
import blockclipboardcornerStyles from './blockclipboardcorner.scss';

import BlockClipBoardButton from '../BlockClipBoardButton';

type Props = {|
    value: Object,
    onPasteBlocks: (items: array<object>) => void,
|};

export default class BlockClipBoardCorner extends React.PureComponent<Props> {




    render() {
        const {value,onPasteBlocks} = this.props;

        return (
            <div className={blockclipboardcornerStyles.blockclipboardcorner}>
                    <BlockClipBoardButton value={value} onPasteBlocks={onPasteBlocks} />
            </div>
        );
    }
}


