// @flow
import React from 'react';
import {render, mount, shallow} from 'enzyme';
import Custom from '../Custom';

jest.mock('../../../utils/Translator', () => ({
    translate: jest.fn((key) => key),
}));

test('Custom should render', () => {
    const onChange = jest.fn();
    expect(render(<Custom onBlur={jest.fn()} onChange={onChange} value="My value" />)).toMatchSnapshot();
});

test('Custom should render with autocomplete off', () => {
    const onChange = jest.fn();
    expect(
        render(<Custom autocomplete="off" disabled={true} onChange={onChange} value="My value" />)
    ).toMatchSnapshot();
});

test('Custom should render as headline', () => {
    expect(render(<Custom headline={true} onChange={jest.fn()} value="My value" />)).toMatchSnapshot();
});

test('Custom should render with invalid value', () => {
    const onChange = jest.fn();
    expect(render(<Custom onBlur={jest.fn()} onChange={onChange} valid={false} value="My value" />)).toMatchSnapshot();
});

test('Custom should render when disabled', () => {
    const onChange = jest.fn();
    expect(render(<Custom disabled={true} onChange={onChange} value="My value" />)).toMatchSnapshot();
});

test('Custom should render with icon', () => {
    const onChange = jest.fn();
    expect(render(<Custom icon="su-pen" onBlur={jest.fn()} onChange={onChange} value="My value" />)).toMatchSnapshot();
});

test('Custom should render with custommode', () => {
    const onChange = jest.fn();
    expect(render(
        <Custom customMode="numeric" onBlur={jest.fn()} onChange={onChange} value="My value" />
    )).toMatchSnapshot();
});

test('Custom should render with type', () => {
    const onChange = jest.fn();
    expect(render(
        <Custom onBlur={jest.fn()} onChange={onChange} type="password" value="My value" />
    )).toMatchSnapshot();
});

test('Custom should render with placeholder', () => {
    const onChange = jest.fn();
    expect(render(
        <Custom onBlur={jest.fn()} onChange={onChange} placeholder="My placeholder" value="My value" />
    )).toMatchSnapshot();
});

test('Custom should render with value', () => {
    const onChange = jest.fn();
    expect(render(<Custom onBlur={jest.fn()} onChange={onChange} value="My value" />)).toMatchSnapshot();
});

test('Custom should render undefined value as empty string', () => {
    const onChange = jest.fn();
    expect(render(<Custom onBlur={jest.fn()} onChange={onChange} value={undefined} />)).toMatchSnapshot();
});

test('Custom should render with a character counter', () => {
    expect(render(<Custom maxCharacters={2} onBlur={jest.fn()} onChange={jest.fn()} value="asdf" />)).toMatchSnapshot();
});

test('Custom should render with a segment counter', () => {
    expect(render(
        <Custom
            maxSegments={3}
            onBlur={jest.fn()}
            onChange={jest.fn()}
            segmentDelimiter=","
            value="keyword1, keyword2"
        />
    )).toMatchSnapshot();
});

test('Custom should call the callback when the custom changes', () => {
    const onChange = jest.fn();
    const custom = shallow(<Custom onBlur={jest.fn()} onChange={onChange} value="My value" />);
    const event = {currentTarget: {value: 'my-value'}};
    custom.find('custom').simulate('change', event);
    expect(onChange).toHaveBeenCalledWith('my-value', event);
});

test('Custom should call the callback with undefined if the custom value is removed', () => {
    const onChange = jest.fn();
    const custom = shallow(<Custom onBlur={jest.fn()} onChange={onChange} value="My value" />);
    const event = {currentTarget: {value: ''}};
    custom.find('custom').simulate('change', event);
    expect(onChange).toHaveBeenCalledWith(undefined, event);
});

test('Custom should call the callback when icon was clicked', () => {
    const onChange = jest.fn();
    const handleIconClick = jest.fn();
    const custom = mount(<Custom icon="su-pen" onChange={onChange} onIconClick={handleIconClick} value="My value" />);
    custom.find('Icon').simulate('click');
    expect(handleIconClick).toHaveBeenCalled();
});

test('Custom should call the given focus callback', () => {
    const onFocusSpy = jest.fn();

    const custom = mount(<Custom icon="su-pen" onChange={jest.fn()} onFocus={onFocusSpy} value="My value" />);

    expect(onFocusSpy).not.toHaveBeenCalled();
    custom.find('custom').simulate('focus');
    expect(onFocusSpy).toHaveBeenCalled();
});

test('Custom should render with a loader', () => {
    const onChange = jest.fn();
    expect(render(<Custom loading={true} onBlur={jest.fn()} onChange={onChange} value={undefined} />)).toMatchSnapshot();
});

test('Custom should render collapsed', () => {
    expect(render(<Custom collapsed={true} onChange={jest.fn()} value={undefined} />)).toMatchSnapshot();
});

test('Custom should render append container when onClearClick callback is provided', () => {
    expect(render(<Custom onChange={jest.fn()} onClearClick={jest.fn()} value={undefined} />)).toMatchSnapshot();
});

test('Custom should render append container with icon when onClearClick callback is provided and value is set', () => {
    expect(render(<Custom onChange={jest.fn()} onClearClick={jest.fn()} value="test" />)).toMatchSnapshot();
});

test('Custom should should call the callback when clear icon was clicked', () => {
    const onClearClick = jest.fn();
    const custom = mount(<Custom onChange={jest.fn()} onClearClick={onClearClick} value="My value" />);
    custom.find('Icon').simulate('click');
    expect(onClearClick).toHaveBeenCalled();
});

test('Custom should render with dark skin', () => {
    expect(
        render(<Custom icon="su-pen" onChange={jest.fn()} onClearClick={jest.fn()} skin="dark" value={undefined} />)
    ).toMatchSnapshot();
});

test('Custom should render with type number with attributes', () => {
    expect(render(
        <Custom max={50} min={10} onBlur={jest.fn()} onChange={jest.fn()} step={5} type="number" value={25} />)
    ).toMatchSnapshot();
});
