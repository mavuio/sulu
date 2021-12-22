// @flow
import React from 'react';
import {render} from 'enzyme';
import BlockClipBoardButton from '../BlockClipBoardButton';

test('Render a blockclipboardbutton', () => {
    expect(render(<BlockClipBoardButton>Hello world</BlockClipBoardButton>)).toMatchSnapshot();
});
