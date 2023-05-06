module.exports = {
    module: {
        rules: [
            {
                test: /\.s[ac]ss$/i,
                use: [
                    "style-loader",
                    "css-loader",
                    {
                        loader: "sass-loader",
                        options: {
                            sassOptions: (loaderContext) => {
                                // More information about available properties https://webpack.js.org/api/loaders/
                                const { resourcePath, rootContext } = loaderContext;
                                const relativePath = path.relative(rootContext, resourcePath);

                                if (relativePath === "styles/foo.scss") {
                                    return {
                                        includePaths: ["absolute/path/c", "absolute/path/d"],
                                    };
                                }

                                return {
                                    includePaths: ["absolute/path/a", "absolute/path/b"],
                                };
                            },
                        },
                    },
                ],
            },
        ],
    },
};