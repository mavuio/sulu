// @flow
import React from 'react';
import {render} from 'enzyme';
import BlockClipBoardMenu from '../BlockClipBoardMenu';

test('Render a blockclipboardmenu', () => {
    expect(render(<BlockClipBoardMenu>Hello world</BlockClipBoardMenu>)).toMatchSnapshot();
});
