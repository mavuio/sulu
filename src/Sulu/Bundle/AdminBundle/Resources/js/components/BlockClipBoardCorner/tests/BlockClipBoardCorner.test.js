// @flow
import React from 'react';
import {render} from 'enzyme';
import BlockClipBoardCorner from '../BlockClipBoardCorner';

test('Render a blockclipboardcorner', () => {
    expect(render(<BlockClipBoardCorner>Hello world</BlockClipBoardCorner>)).toMatchSnapshot();
});
