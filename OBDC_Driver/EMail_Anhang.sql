USE [Medicarehsw]
GO

/****** Object:  Table [dbo].[EMail_Anhang]    Script Date: 12.02.2025 14:25:15 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[EMail_Anhang](
	[ID] [int] NOT NULL,
	[Pos] [int] NOT NULL,
	[Mail] [ntext] NOT NULL,
	[Name] [varchar](100) NOT NULL,
 CONSTRAINT [pkAnhang] PRIMARY KEY CLUSTERED 
(
	[ID] ASC,
	[Pos] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO

