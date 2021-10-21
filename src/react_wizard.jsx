import "@babel/polyfill";
import React, {useEffect, useState} from "react";
import * as ReactDOM from "react-dom";
import {or, rankWith, schemaTypeIs, scopeEndsWith} from '@jsonforms/core';
import {JsonForms} from '@jsonforms/react';
import {materialRenderers} from '@jsonforms/material-renderers';
import JsonRefs from 'json-refs';
import {Grid, TextField, Typography} from '@material-ui/core';
import {Autocomplete} from '@material-ui/lab';
import throttle from 'lodash/throttle';

const autocompleteControlTester = rankWith(
	1000,
	or(scopeEndsWith('autocomplete_custom'), schemaTypeIs('autocomplete_custom'))
);

const AutocompleteControl = (props) => {

	const [value, setValue] = React.useState(null);
	const [inputValue, setInputValue] = React.useState('');
	const [options, setOptions] = React.useState([]);

	const fetchResults = React.useMemo(
		() =>
			throttle((request, callback) => {
				(async () => {
					const location = adminfolder + 'json/' + props.uischema.location;
					const params = new URLSearchParams();
					params.append('search', request.input);
					const settings = {
						method: 'POST',
						body: params
					};
					try {
						const fetchResponse = await fetch(location, settings);
						const response = await fetchResponse.json();
						callback(response);
					} catch (e) {
						return e;
					}
				})();
			}, 200),
		[],
	);

	React.useEffect(() => {
		let active = true;

		if (inputValue === '') {
			setOptions(value ? [value] : []);
			return undefined;
		}

		fetchResults({input: inputValue}, (results) => {
			if (active) {
				let newOptions = [];

				if (value) {
					newOptions = [value];
				}

				if (results) {
					newOptions = [...newOptions, ...results];
				}

				setOptions(newOptions);
			}
		});

		return () => {
			active = false;
		};
	}, [value, inputValue, fetch]);

	return (
		<Autocomplete
			style={{width: 300}}
			getOptionLabel={(option) => (typeof option === 'string' ? option : option.name)}
			filterOptions={(x) => x}
			options={options}
			autoComplete
			includeInputInList
			filterSelectedOptions
			value={value}
			onChange={(event, newValue) => {
				setOptions(newValue ? [newValue, ...options] : options);
				setValue(newValue);
			}}
			onInputChange={(event, newInputValue) => {
				setInputValue(newInputValue);
			}}
			renderOption={(option) => {
				return (
					<Grid container alignItems="center">
						{option.label &&
						<Grid item xs>
							<span>{option.label}</span>
						</Grid>
						}
						<Typography variant="body2" color="textSecondary">
							{option.name}
						</Typography>
					</Grid>

				);
			}}
			renderInput={(params) => (
				<TextField
					{...params}
					label={props.uischema.label}
					fullWidth
					InputProps={{
						...params.InputProps,
						endAdornment: (
							<React.Fragment>
								{params.InputProps.endAdornment}
							</React.Fragment>
						),
					}}
				/>
			)}
		/>
	);
}

const renderers = [
	...materialRenderers,
	//register custom renderers
	{tester: autocompleteControlTester, renderer: AutocompleteControl},
];

const WizardForm = () => {
	const [data, setData] = useState({});
	const [resolvedSchema, setSchema] = useState();
	const [resolvedUISchema, setUISchema] = useState();

	useEffect(() => {
		JsonRefs.resolveRefsAt(adminfolder + 'json/' + jsonPage + '?schema=1&step=1').then(res => {
			setSchema(res.resolved.json_schema)
			setUISchema(res.resolved.ui_schema)
		});

		window.addEventListener('dataEdit', (event) => {
			setData(event.data.data);
		});
	}, []);
	return (
		<div>
			<JsonForms
				schema={resolvedSchema}
				uischema={resolvedUISchema}
				data={data}
				renderers={renderers}
				onChange={({data, errors}) => {
					window.jsonForms = {data, errors}
				}}
			/>
		</div>
	);
}

$(function () {
	ReactDOM.render(
		<WizardForm/>,
		document.getElementById('wizard_container')
	);
});